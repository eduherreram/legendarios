<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreFinanzasMovimientoRequest;
use App\Models\Departamento;
use App\Models\FinanzasMovimiento;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FinanzasController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', FinanzasMovimiento::class);

        $user = $request->user();

        $departamentoId = $request->integer('departamento_id') ?: null;
        $mes = $request->string('mes')->toString();

        $desde = '';
        $hasta = '';
        if ($mes !== '') {
            $desde = $mes.'-01';
            $hasta = now()->createFromFormat('Y-m-d', $desde)->endOfMonth()->format('Y-m-d');
        }

        $departamentosQuery = Departamento::query()->orderBy('nombre');

        $departamentos = $departamentosQuery->get(['id', 'nombre']);

        $movimientos = FinanzasMovimiento::query()
            ->with(['departamento', 'creadoPor'])
            ->when($departamentoId, fn ($q) => $q->where('departamento_id', $departamentoId))
            ->when($desde !== '', fn ($q) => $q->whereDate('fecha', '>=', $desde))
            ->when($hasta !== '', fn ($q) => $q->whereDate('fecha', '<=', $hasta))
            ->orderByDesc('fecha')
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        $totalesQuery = FinanzasMovimiento::query()
            ->when($departamentoId, fn ($q) => $q->where('departamento_id', $departamentoId))
            ->when($desde !== '', fn ($q) => $q->whereDate('fecha', '>=', $desde))
            ->when($hasta !== '', fn ($q) => $q->whereDate('fecha', '<=', $hasta));

        $totalesGlobales = $totalesQuery
            ->selectRaw("SUM(CASE WHEN tipo = 'ingreso' THEN monto ELSE 0 END) as ingresos")
            ->selectRaw("SUM(CASE WHEN tipo = 'egreso' THEN monto ELSE 0 END) as egresos")
            ->first();

        $totalIngresos = (int) ($totalesGlobales->ingresos ?? 0);
        $totalEgresos = (int) ($totalesGlobales->egresos ?? 0);
        $disponible = $totalIngresos - $totalEgresos;

        $balancesQuery = FinanzasMovimiento::query()
            ->selectRaw("departamento_id, SUM(CASE WHEN tipo = 'ingreso' THEN monto ELSE 0 END) as ingresos")
            ->selectRaw("SUM(CASE WHEN tipo = 'egreso' THEN monto ELSE 0 END) as egresos")
            ->groupBy('departamento_id');

        $balances = $balancesQuery
            ->get()
            ->keyBy('departamento_id');

        return view('admin.finanzas.index', [
            'departamentos' => $departamentos,
            'movimientos' => $movimientos,
            'balances' => $balances,
            'totales' => [
                'ingresos' => $totalIngresos,
                'egresos' => $totalEgresos,
                'disponible' => $disponible,
            ],
            'filters' => [
                'departamento_id' => $departamentoId,
                'mes' => $mes,
            ],
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', FinanzasMovimiento::class);

        $departamentos = Departamento::query()->orderBy('nombre')->get(['id', 'nombre']);

        return view('admin.finanzas.form', [
            'departamentos' => $departamentos,
        ]);
    }

    public function departamento(Request $request, Departamento $departamento): View
    {
        $this->authorize('view', $departamento);

        $tipo = $request->string('tipo')->toString();
        $desde = $request->string('desde')->toString();
        $hasta = $request->string('hasta')->toString();

        $movimientos = FinanzasMovimiento::query()
            ->with(['creadoPor'])
            ->where('departamento_id', $departamento->id)
            ->when(in_array($tipo, ['ingreso', 'egreso'], true), fn ($q) => $q->where('tipo', $tipo))
            ->when($desde !== '', fn ($q) => $q->whereDate('fecha', '>=', $desde))
            ->when($hasta !== '', fn ($q) => $q->whereDate('fecha', '<=', $hasta))
            ->orderByDesc('fecha')
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        $totales = FinanzasMovimiento::query()
            ->where('departamento_id', $departamento->id)
            ->when(in_array($tipo, ['ingreso', 'egreso'], true), fn ($q) => $q->where('tipo', $tipo))
            ->when($desde !== '', fn ($q) => $q->whereDate('fecha', '>=', $desde))
            ->when($hasta !== '', fn ($q) => $q->whereDate('fecha', '<=', $hasta))
            ->selectRaw("SUM(CASE WHEN tipo = 'ingreso' THEN monto ELSE 0 END) as ingresos")
            ->selectRaw("SUM(CASE WHEN tipo = 'egreso' THEN monto ELSE 0 END) as egresos")
            ->first();

        $ingresos = (int) ($totales->ingresos ?? 0);
        $egresos = (int) ($totales->egresos ?? 0);
        $balance = $ingresos - $egresos;

        return view('admin.finanzas.departamento', [
            'departamento' => $departamento,
            'movimientos' => $movimientos,
            'resumen' => [
                'ingresos' => $ingresos,
                'egresos' => $egresos,
                'balance' => $balance,
            ],
            'filters' => [
                'tipo' => $tipo,
                'desde' => $desde,
                'hasta' => $hasta,
            ],
        ]);
    }

    public function store(StoreFinanzasMovimientoRequest $request): RedirectResponse
    {
        $departamento = Departamento::query()->findOrFail((int) $request->validated('departamento_id'));
        $this->authorize('create', [FinanzasMovimiento::class, $departamento]);

        FinanzasMovimiento::query()->create([
            ...$request->validated(),
            'creado_por' => auth()->id(),
        ]);

        return redirect()->route('admin.finanzas.index')->with('success', 'Movimiento financiero creado correctamente.');
    }

    public function destroy(FinanzasMovimiento $movimiento): RedirectResponse
    {
        $this->authorize('delete', $movimiento);

        $movimiento->delete();

        return redirect()->route('admin.finanzas.index')->with('success', 'Movimiento financiero eliminado correctamente.');
    }
}

