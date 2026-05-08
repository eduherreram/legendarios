<?php

namespace App\Providers;

use App\Models\Departamento;
use App\Models\DepartamentoActividad;
use App\Models\FinanzasMovimiento;
use App\Models\Asistencia;
use App\Policies\AsistenciaPolicy;
use App\Policies\DepartamentoPolicy;
use App\Policies\DepartamentoActividadPolicy;
use App\Policies\FinanzasMovimientoPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Departamento::class => DepartamentoPolicy::class,
        DepartamentoActividad::class => DepartamentoActividadPolicy::class,
        Asistencia::class => AsistenciaPolicy::class,
        FinanzasMovimiento::class => FinanzasMovimientoPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();
    }
}
