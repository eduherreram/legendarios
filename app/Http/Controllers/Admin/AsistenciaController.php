<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Asistencia;
use App\Models\DepartamentoActividad;
use App\Models\FinanzasMovimiento;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AsistenciaController extends Controller
{
    public function index(DepartamentoActividad $departamentoActividad): View
    {
        $this->authorize('view', $departamentoActividad);

        $departamentoActividad->load(['actividad', 'departamento']);

        $asistencias = Asistencia::query()
            ->with('user')
            ->where('departamento_actividad_id', $departamentoActividad->id)
            ->whereHas('user', function ($q) {
                $q->where('estado', 'activo');
            })
            ->orderBy('user_id')
            ->get();

        return view('admin.actividades.asistencia', [
            'departamentoActividad' => $departamentoActividad,
            'asistencias' => $asistencias,
        ]);
    }

    public function update(Request $request, DepartamentoActividad $departamentoActividad, Asistencia $asistencia): RedirectResponse
    {
        $this->authorize('view', $departamentoActividad);

        if ((int) $asistencia->departamento_actividad_id !== (int) $departamentoActividad->id) {
            abort(404);
        }

        $asistencia->loadMissing(['departamentoActividad', 'user']);
        $this->authorize('update', $asistencia);

        $data = $request->validate([
            'estado' => ['required', 'in:presente,ausente'],
            'pago_accion' => ['nullable', 'in:ninguno,total,abono,freepass'],
            'monto' => ['nullable', 'integer', 'min:1'],
        ]);

        $precio = (int) ($departamentoActividad->actividad?->precio ?? 0);
        $accion = $data['pago_accion'] ?? 'ninguno';

        $payload = [
            'estado' => $data['estado'],
            'marcada_en' => now(),
            'marcada_por' => auth()->id(),
        ];

        if ($accion === 'total' && $precio > 0) {
            $monto = max(0, $precio - (int) ($asistencia->pago_monto_pagado ?? 0));
            $payload['pago_estado'] = 'pagado';
            $payload['pago_monto_pagado'] = $precio;
            $payload['pago_registrado_en'] = now();
            $payload['pago_registrado_por'] = auth()->id();

            if ($monto > 0) {
                FinanzasMovimiento::query()->create([
                    'departamento_id' => $departamentoActividad->departamento_id,
                    'departamento_actividad_id' => $departamentoActividad->id,
                    'tipo' => 'ingreso',
                    'monto' => $monto,
                    'descripcion' => 'Pago actividad: '.($departamentoActividad->actividad?->nombre ?? '-').' - '.($asistencia->user?->name ?? '-'),
                    'fecha' => now()->toDateString(),
                    'creado_por' => auth()->id(),
                ]);
            }
        } elseif ($accion === 'abono') {
            $monto = (int) ($data['monto'] ?? 0);
            if ($monto <= 0) {
                return redirect()
                    ->route('admin.actividades.asistencia', $departamentoActividad)
                    ->with('warning', 'Ingresa un monto valido para el abono.');
            }

            $pagado = (int) ($asistencia->pago_monto_pagado ?? 0) + $monto;
            $payload['pago_estado'] = $precio > 0 && $pagado >= $precio ? 'pagado' : 'abonado';
            $payload['pago_monto_pagado'] = $pagado;
            $payload['pago_registrado_en'] = now();
            $payload['pago_registrado_por'] = auth()->id();

            FinanzasMovimiento::query()->create([
                'departamento_id' => $departamentoActividad->departamento_id,
                'departamento_actividad_id' => $departamentoActividad->id,
                'tipo' => 'ingreso',
                'monto' => $monto,
                'descripcion' => 'Abono actividad: '.($departamentoActividad->actividad?->nombre ?? '-').' - '.($asistencia->user?->name ?? '-'),
                'fecha' => now()->toDateString(),
                'creado_por' => auth()->id(),
            ]);
        } elseif ($accion === 'freepass') {
            $payload['pago_estado'] = 'freepass';
            $payload['pago_monto_pagado'] = 0;
            $payload['pago_registrado_en'] = now();
            $payload['pago_registrado_por'] = auth()->id();
        }

        $asistencia->forceFill($payload)->save();

        $this->syncDepartamentoPago($departamentoActividad);

        return redirect()
            ->route('admin.actividades.asistencia', $departamentoActividad)
            ->with('success', 'Asistencia actualizada correctamente.');
    }

    private function syncDepartamentoPago(DepartamentoActividad $departamentoActividad): void
    {
        $precio = (int) ($departamentoActividad->actividad?->precio ?? 0);
        $asistencias = Asistencia::query()
            ->where('departamento_actividad_id', $departamentoActividad->id)
            ->whereHas('user', fn ($query) => $query->where('estado', 'activo'))
            ->get(['pago_estado', 'pago_monto_pagado']);

        $total = $precio * $asistencias->reject(fn ($a) => $a->pago_estado === 'freepass')->count();
        $pagado = (int) $asistencias->sum('pago_monto_pagado');
        $estado = 'pendiente';

        if ($total <= 0 || $pagado >= $total) {
            $estado = 'pagado';
        } elseif ($pagado > 0 || $asistencias->contains(fn ($a) => in_array($a->pago_estado, ['abonado', 'freepass'], true))) {
            $estado = 'pagado_parcial';
        }

        $departamentoActividad->forceFill([
            'pago_estado' => $estado,
            'pago_monto_total' => $total,
            'pago_monto_pagado' => $pagado,
        ])->save();
    }
}
