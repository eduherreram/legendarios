<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreActividadRequest;
use App\Http\Requests\Admin\StoreActividadAbonoRequest;
use App\Models\Actividad;
use App\Models\Departamento;
use App\Models\DepartamentoActividad;
use App\Models\FinanzasMovimiento;
use App\Services\ActividadService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ActividadController extends Controller
{
    public function __construct(
        private readonly ActividadService $service,
    ) {
    }

    public function index(Request $request): View
    {
        $q = trim((string) $request->query('q', ''));

        $actividades = Actividad::query()
            ->when($q !== '', function ($query) use ($q) {
                $query->where('nombre', 'like', "%{$q}%");
            })
            ->orderByDesc('fecha')
            ->paginate(15)
            ->withQueryString();

        return view('admin.actividades.index', [
            'actividades' => $actividades,
            'q' => $q,
        ]);
    }

    public function mis(): View
    {
        $user = auth()->user();
        if (! $user || ! $user->departamento_id) {
            abort(403);
        }

        $items = DepartamentoActividad::query()
            ->with(['actividad'])
            ->where('departamento_id', $user->departamento_id)
            ->orderByDesc('id')
            ->paginate(15);

        return view('admin.actividades.mis', [
            'items' => $items,
        ]);
    }

    public function create(): View
    {
        return view('admin.actividades.form');
    }

    public function store(StoreActividadRequest $request): RedirectResponse
    {
        $actividad = $this->service->createGlobal($request->validated(), auth()->id());

        return redirect()
            ->route('admin.actividades.show', $actividad)
            ->with('success', 'Actividad creada correctamente. Se genero un formulario de compromiso.')
            ->with('actividad_registration_url', $actividad->registrationUrl());
    }

    public function show(Actividad $actividad): View
    {
        $departamentos = DepartamentoActividad::query()
            ->with(['departamento'])
            ->withCount('asistencias')
            ->where('actividad_id', $actividad->id)
            ->orderBy('departamento_id')
            ->get();

        $resumenPagos = [
            'monto_total' => (int) $departamentos->sum('pago_monto_total'),
            'monto_pagado' => (int) $departamentos->sum('pago_monto_pagado'),
        ];

        $compromisosCount = $actividad->compromisos()->count();

        return view('admin.actividades.show', [
            'actividad' => $actividad,
            'departamentos' => $departamentos,
            'resumenPagos' => $resumenPagos,
            'compromisosCount' => $compromisosCount,
        ]);
    }

    public function abonar(StoreActividadAbonoRequest $request, DepartamentoActividad $departamentoActividad): RedirectResponse
    {
        $departamentoActividad->loadMissing(['departamento', 'actividad']);
        $this->authorize('view', $departamentoActividad);

        $departamento = Departamento::query()->findOrFail((int) $departamentoActividad->departamento_id);
        $this->authorize('create', [FinanzasMovimiento::class, $departamento]);

        $data = $request->validated();

        FinanzasMovimiento::query()->create([
            'departamento_id' => $departamentoActividad->departamento_id,
            'departamento_actividad_id' => $departamentoActividad->id,
            'tipo' => 'ingreso',
            'monto' => (int) $data['monto'],
            'descripcion' => 'Abono actividad: '.($departamentoActividad->actividad?->nombre ?? '-'),
            'fecha' => $data['fecha'],
            'creado_por' => auth()->id(),
        ]);

        $pagado = (int) FinanzasMovimiento::query()
            ->where('departamento_actividad_id', $departamentoActividad->id)
            ->where('tipo', 'ingreso')
            ->sum('monto');

        $total = (int) ($departamentoActividad->pago_monto_total ?? 0);
        $nuevoEstado = 'pendiente';
        if ($total <= 0) {
            $nuevoEstado = 'pagado';
        } elseif ($pagado >= $total) {
            $nuevoEstado = 'pagado';
        } elseif ($pagado > 0) {
            $nuevoEstado = 'pagado_parcial';
        }

        $departamentoActividad->forceFill([
            'pago_monto_pagado' => $pagado,
            'pago_estado' => $nuevoEstado,
        ])->save();

        return redirect()
            ->route('admin.actividades.show', $departamentoActividad->actividad_id)
            ->with('success', 'Abono registrado correctamente.');
    }

    public function close(Actividad $actividad): RedirectResponse
    {
        if ($actividad->estado === 'cerrada') {
            return redirect()->route('admin.actividades.show', $actividad)->with('info', 'La actividad ya estaba cerrada.');
        }

        $this->service->closeActividad($actividad, (int) auth()->id());

        return redirect()->route('admin.actividades.index')->with('success', 'Actividad cerrada correctamente.');
    }
}
