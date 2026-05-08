<?php

namespace App\Policies;

use App\Models\Asistencia;
use App\Models\Departamento;
use App\Models\User;

class AsistenciaPolicy
{
    public function before(User $user, string $ability): bool|null
    {
        if ($user->hasAnyRole(['Administrador', 'Supervisor'])) {
            return true;
        }

        return null;
    }

    public function update(User $user, Asistencia $asistencia): bool
    {
        if (! ($user->hasRole('Encargado') || $user->hasRole('Líder'))) {
            return false;
        }

        $departamentoActividad = $asistencia->departamentoActividad;
        if (! $departamentoActividad) {
            return false;
        }

        if ((int) $user->departamento_id === (int) $departamentoActividad->departamento_id) {
            return true;
        }

        $departamento = Departamento::query()->find($departamentoActividad->departamento_id);
        if (! $departamento) {
            return false;
        }

        return (int) $departamento->lider_id === (int) $user->id
            || (int) $departamento->encargado_id === (int) $user->id;
    }
}
