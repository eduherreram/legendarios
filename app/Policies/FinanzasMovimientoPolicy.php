<?php

namespace App\Policies;

use App\Models\Departamento;
use App\Models\FinanzasMovimiento;
use App\Models\User;

class FinanzasMovimientoPolicy
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
        return false;
    }

    public function view(User $user, FinanzasMovimiento $movimiento): bool
    {
        return false;
    }

    public function create(User $user, ?Departamento $departamento = null): bool
    {
        return false;
    }

    public function delete(User $user, FinanzasMovimiento $movimiento): bool
    {
        return false;
    }
}
