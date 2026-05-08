<?php

namespace App\Policies;

use App\Models\Departamento;
use App\Models\User;

class DepartamentoPolicy
{
    public function before(User $user, string $ability): bool|null
    {
        if ($user->hasAnyRole(['Administrador', 'Supervisor', 'Finanzas'])) {
            return true;
        }

        return null;
    }

    public function viewAny(User $user): bool
    {
        return $user->hasRole('Encargado')
            || $user->hasRole('Líder');
    }

    public function view(User $user, Departamento $departamento): bool
    {
        if ((int) $user->departamento_id === (int) $departamento->id) {
            return true;
        }

        return (int) $departamento->supervisor_id === (int) $user->id
            || (int) $departamento->lider_id === (int) $user->id
            || (int) $departamento->encargado_id === (int) $user->id;
    }

    public function create(User $user): bool
    {
        return $user->hasRole('Supervisor');
    }

    public function update(User $user, Departamento $departamento): bool
    {
        return $user->hasRole('Supervisor');
    }

    public function delete(User $user, Departamento $departamento): bool
    {
        return $user->hasRole('Supervisor');
    }
}
