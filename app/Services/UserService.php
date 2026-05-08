<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UserService
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): User
    {
        return DB::transaction(function () use ($data) {
            $user = User::query()->create([
                'numero_legendario' => $data['numero_legendario'] ?? null,
                'nombre' => $data['nombre'],
                'apellido' => $data['apellido'],
                'rut' => $data['rut'],
                'fecha_nacimiento' => $data['fecha_nacimiento'],
                'enfermedad' => (bool) ($data['enfermedad'] ?? false),
                'talla' => $data['talla'] ?? null,
                'iglesia' => $data['iglesia'] ?? null,
                'es_pastor' => (bool) ($data['es_pastor'] ?? false),
                'direccion_calle' => $data['direccion_calle'] ?? null,
                'direccion_numero' => $data['direccion_numero'] ?? null,
                'direccion_comuna' => $data['direccion_comuna'] ?? null,
                'direccion_region' => $data['direccion_region'] ?? null,
                'direccion_pais' => $data['direccion_pais'] ?? null,
                'telefono' => $data['telefono'] ?? null,
                'email' => $data['email'],
                'password' => Str::password(16),
                'nombre_contacto_emergencia' => $data['nombre_contacto_emergencia'],
                'parentesco_contacto_emergencia' => $data['parentesco_contacto_emergencia'],
                'telefono_contacto_emergencia' => $data['telefono_contacto_emergencia'],
                'estado' => $data['estado'],
                'fecha_inscripcion' => now(),
                'departamento_id' => $data['departamento_id'] ?? null,
            ]);

            if (method_exists($user, 'syncRoles')) {
                $user->syncRoles([$data['role']]);
            }

            return $user;
        });
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(User $user, array $data): User
    {
        return DB::transaction(function () use ($user, $data) {
            $user->forceFill([
                'numero_legendario' => $data['numero_legendario'] ?? null,
                'nombre' => $data['nombre'],
                'apellido' => $data['apellido'],
                'rut' => $data['rut'],
                'fecha_nacimiento' => $data['fecha_nacimiento'],
                'enfermedad' => (bool) ($data['enfermedad'] ?? false),
                'talla' => $data['talla'] ?? null,
                'iglesia' => $data['iglesia'] ?? null,
                'es_pastor' => (bool) ($data['es_pastor'] ?? false),
                'direccion_calle' => $data['direccion_calle'] ?? null,
                'direccion_numero' => $data['direccion_numero'] ?? null,
                'direccion_comuna' => $data['direccion_comuna'] ?? null,
                'direccion_region' => $data['direccion_region'] ?? null,
                'direccion_pais' => $data['direccion_pais'] ?? null,
                'telefono' => $data['telefono'] ?? null,
                'email' => $data['email'],
                'nombre_contacto_emergencia' => $data['nombre_contacto_emergencia'],
                'parentesco_contacto_emergencia' => $data['parentesco_contacto_emergencia'],
                'telefono_contacto_emergencia' => $data['telefono_contacto_emergencia'],
                'estado' => $data['estado'],
                'departamento_id' => $data['departamento_id'] ?? null,
            ]);

            $user->save();

            if (method_exists($user, 'syncRoles')) {
                $user->syncRoles([$data['role']]);
            }

            return $user;
        });
    }
}
