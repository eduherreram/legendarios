<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasRole('Administrador') ?? false;
    }

    public function rules(): array
    {
        return [
            'numero_legendario' => ['nullable', 'string', 'max:255', 'unique:users,numero_legendario'],
            'nombre' => ['required', 'string', 'max:255'],
            'apellido' => ['required', 'string', 'max:255'],
            'rut' => ['required', 'string', 'max:50', 'unique:users,rut'],
            'fecha_nacimiento' => ['required', 'date'],
            'enfermedad' => ['sometimes', 'boolean'],
            'talla' => ['nullable', 'in:S,M,L,XL,XXL'],

            'iglesia' => ['nullable', 'string', 'max:255'],
            'es_pastor' => ['sometimes', 'boolean'],

            'direccion_calle' => ['nullable', 'string', 'max:255'],
            'direccion_numero' => ['nullable', 'string', 'max:50'],
            'direccion_comuna' => ['nullable', 'string', 'max:255'],
            'direccion_region' => ['nullable', 'string', 'max:255'],
            'direccion_pais' => ['nullable', 'string', 'max:255'],
            'telefono' => ['nullable', 'string', 'max:50'],

            'email' => ['required', 'email', 'max:255', 'unique:users,email'],

            'nombre_contacto_emergencia' => ['required', 'string', 'max:255'],
            'parentesco_contacto_emergencia' => ['required', 'string', 'max:255'],
            'telefono_contacto_emergencia' => ['required', 'string', 'max:50'],

            'estado' => ['required', 'in:activo,inactivo,pendiente'],
            'departamento_id' => ['nullable', 'integer', 'exists:departamentos,id'],

            'role' => [
                'required',
                'string',
                Rule::exists('roles', 'name'),
            ],
        ];
    }
}
