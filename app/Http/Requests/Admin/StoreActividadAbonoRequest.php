<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreActividadAbonoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasAnyRole(['Administrador', 'Supervisor', 'Finanzas']) ?? false;
    }

    public function rules(): array
    {
        return [
            'monto' => ['required', 'integer', 'min:1'],
            'fecha' => ['required', 'date'],
        ];
    }
}
