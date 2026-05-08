<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreFinanzasMovimientoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasAnyRole(['Administrador', 'Supervisor', 'Finanzas']) ?? false;
    }

    public function rules(): array
    {
        return [
            'departamento_id' => ['required', 'integer', 'exists:departamentos,id'],
            'departamento_actividad_id' => ['nullable', 'integer', 'exists:departamento_actividades,id'],
            'tipo' => ['required', 'in:ingreso,egreso'],
            'monto' => ['required', 'integer', 'min:1'],
            'descripcion' => ['required', 'string', 'max:255'],
            'fecha' => ['required', 'date'],
        ];
    }
}
