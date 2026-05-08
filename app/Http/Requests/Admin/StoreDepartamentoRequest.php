<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreDepartamentoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasAnyRole(['Administrador', 'Supervisor']) ?? false;
    }

    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:255', 'unique:departamentos,nombre'],
            'supervisor_id' => ['nullable', 'integer', 'exists:users,id'],
            'lider_id' => ['nullable', 'integer', 'exists:users,id'],
            'encargado_id' => ['nullable', 'integer', 'exists:users,id'],
        ];
    }
}
