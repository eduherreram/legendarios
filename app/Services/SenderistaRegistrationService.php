<?php

namespace App\Services;

use App\Models\QrRegistrationToken;
use App\Models\SenderistaRegistration;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class SenderistaRegistrationService
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function register(QrRegistrationToken $token, array $data): User
    {
        return DB::transaction(function () use ($token, $data) {
            $exists = User::query()
                ->where('rut', $data['rut'])
                ->orWhere('email', $data['email'])
                ->exists();

            if ($exists) {
                throw ValidationException::withMessages([
                    'rut' => ['Ya existe un usuario con este RUT o email.'],
                ]);
            }

            $user = User::query()->create([
                'numero_legendario' => null,
                'nombre' => $data['nombre'],
                'apellido' => $data['apellido'],
                'rut' => $data['rut'],
                'fecha_nacimiento' => $data['fecha_nacimiento'],
                'enfermedad' => false,
                'talla' => null,
                'iglesia' => null,
                'es_pastor' => false,
                'direccion_calle' => $data['direccion_calle'],
                'direccion_numero' => $data['direccion_numero'],
                'direccion_comuna' => $data['direccion_comuna'],
                'direccion_region' => $data['direccion_region'],
                'direccion_pais' => $data['direccion_pais'],
                'telefono' => $data['telefono'],
                'email' => $data['email'],
                'password' => Str::password(24),
                'nombre_contacto_emergencia' => $data['nombre_contacto_emergencia'],
                'parentesco_contacto_emergencia' => $data['parentesco_contacto_emergencia'],
                'telefono_contacto_emergencia' => $data['telefono_contacto_emergencia'],
                'estado' => 'activo',
                'fecha_inscripcion' => now(),
                'departamento_id' => null,
            ]);

            if (method_exists($user, 'assignRole')) {
                $user->assignRole('Senderista');
            }

            $token->forceFill([
                'used_at' => now(),
                'used_by_user_id' => $user->id,
            ])->save();

            SenderistaRegistration::query()->create([
                'user_id' => $user->id,
                'qr_registration_token_id' => $token->id,
                'registered_at' => now(),
            ]);

            return $user;
        });
    }
}
