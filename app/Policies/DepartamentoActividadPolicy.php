<?php

namespace App\Policies;

use App\Models\Departamento;
use App\Models\DepartamentoActividad;
use App\Models\User;

class DepartamentoActividadPolicy
{
    public function before(User $user, string $ability): bool|null
    {
        if ($user->hasAnyRole(['Administrador', 'Supervisor'])) {
            return true;
        }

        return null;
    }

    public function view(User $user, DepartamentoActividad $departamentoActividad): bool
    {
        if ((int) $user->departamento_id === (int) $departamentoActividad->departamento_id) {
            return true;
        }

        $departamento = Departamento::query()->find($departamentoActividad->departamento_id);
        if (! $departamento) {
            return false;
        }

        return (int) $departamento->lider_id === (int) $user->id
            || (int) $departamento->encargado_id === (int) $user->id
            || (int) $departamento->supervisor_id === (int) $user->id;
    }
}
