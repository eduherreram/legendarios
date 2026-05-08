<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSenderistaRegistrationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:255'],
            'apellido' => ['required', 'string', 'max:255'],
            'rut' => ['required', 'string', 'max:50'],
            'fecha_nacimiento' => ['required', 'date'],
            'telefono' => ['required', 'string', 'max:50'],
            'email' => ['required', 'email', 'max:255'],

            'direccion_calle' => ['required', 'string', 'max:255'],
            'direccion_numero' => ['required', 'string', 'max:50'],
            'direccion_comuna' => ['required', 'string', 'max:255'],
            'direccion_region' => ['required', 'string', 'max:255'],
            'direccion_pais' => ['required', 'string', 'max:255'],

            'nombre_contacto_emergencia' => ['required', 'string', 'max:255'],
            'parentesco_contacto_emergencia' => ['required', 'string', 'max:255'],
            'telefono_contacto_emergencia' => ['required', 'string', 'max:50'],
        ];
    }
}
