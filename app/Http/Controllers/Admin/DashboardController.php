<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Asistencia;
use App\Models\Actividad;
use App\Models\Departamento;
use App\Models\DepartamentoActividad;
use App\Models\FinanzasMovimiento;
use App\Models\QrCampaign;
use App\Models\QrRegistrationToken;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $totalDepartamentos = Departamento::query()->count();
        $usuariosActivos = User::query()->where('estado', 'activo')->count();
        $usuariosInactivos = User::query()->where('estado', 'inactivo')->count();

        $ingresos = FinanzasMovimiento::query()->where('tipo', 'ingreso')->sum('monto');
        $egresos = FinanzasMovimiento::query()->where('tipo', 'egreso')->sum('monto');
        $balanceGlobal = (int) $ingresos - (int) $egresos;
        $totalActividades = Actividad::query()->count();
        $actividadesAbiertas = Actividad::query()->where('estado', 'abierta')->count();
        $actividadesCerradas = Actividad::query()->where('estado', 'cerrada')->count();
        $tokensDisponibles = QrRegistrationToken::query()
            ->whereNull('used_at')
            ->where(fn ($query) => $query->whereNull('expires_at')->orWhere('expires_at', '>', now()))
            ->count();
        $campanasActivas = QrCampaign::query()->where('activa', true)->count();

        $participacionPorActividad = Asistencia::query()
            ->select('actividades.nombre')
            ->selectRaw("SUM(CASE WHEN asistencias.estado = 'presente' THEN 1 ELSE 0 END) as presentes")
            ->selectRaw("SUM(CASE WHEN asistencias.estado = 'ausente' THEN 1 ELSE 0 END) as ausentes")
            ->join('departamento_actividades', 'departamento_actividades.id', '=', 'asistencias.departamento_actividad_id')
            ->join('actividades', 'actividades.id', '=', 'departamento_actividades.actividad_id')
            ->groupBy('actividades.id', 'actividades.nombre')
            ->orderByDesc('presentes')
            ->limit(6)
            ->get();

        $totalesAsistencia = [
            'presentes' => (int) Asistencia::query()->where('estado', 'presente')->count(),
            'ausentes' => (int) Asistencia::query()->where('estado', 'ausente')->count(),
            'actividades' => (int) DepartamentoActividad::query()->count(),
        ];

        $balancePorDepartamento = Departamento::query()
            ->select('departamentos.id', 'departamentos.nombre')
            ->selectRaw("COALESCE(SUM(CASE WHEN finanzas_movimientos.tipo = 'ingreso' THEN finanzas_movimientos.monto ELSE -finanzas_movimientos.monto END), 0) as balance")
            ->leftJoin('finanzas_movimientos', 'finanzas_movimientos.departamento_id', '=', 'departamentos.id')
            ->groupBy('departamentos.id', 'departamentos.nombre')
            ->orderByDesc('balance')
            ->limit(8)
            ->get();

        $ultimosMovimientos = FinanzasMovimiento::query()
            ->with('departamento:id,nombre')
            ->orderByDesc('fecha')
            ->orderByDesc('id')
            ->limit(8)
            ->get();

        return view('admin.dashboard', [
            'totalDepartamentos' => $totalDepartamentos,
            'usuariosActivos' => $usuariosActivos,
            'usuariosInactivos' => $usuariosInactivos,
            'ingresos' => (int) $ingresos,
            'egresos' => (int) $egresos,
            'balanceGlobal' => $balanceGlobal,
            'totalActividades' => $totalActividades,
            'actividadesAbiertas' => $actividadesAbiertas,
            'actividadesCerradas' => $actividadesCerradas,
            'tokensDisponibles' => $tokensDisponibles,
            'campanasActivas' => $campanasActivas,
            'participacionPorActividad' => $participacionPorActividad,
            'totalesAsistencia' => $totalesAsistencia,
            'balancePorDepartamento' => $balancePorDepartamento,
            'ultimosMovimientos' => $ultimosMovimientos,
        ]);
    }
}
