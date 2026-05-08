<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ConvertSenderistaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasAnyRole(['Administrador', 'Supervisor']) ?? false;
    }

    public function rules(): array
    {
        return [
            'numero_legendario' => ['required', 'string', 'max:255', 'unique:users,numero_legendario'],
            'departamento_id' => ['nullable', 'integer', 'exists:departamentos,id'],
        ];
    }
}
