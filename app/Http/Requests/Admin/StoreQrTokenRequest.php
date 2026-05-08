<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreQrTokenRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasAnyRole(['Administrador', 'Supervisor']) ?? false;
    }

    public function rules(): array
    {
        return [
            'qr_campaign_id' => ['nullable', 'integer', 'exists:qr_campaigns,id'],
            'expires_at' => ['nullable', 'date', 'after:now'],
        ];
    }
}
