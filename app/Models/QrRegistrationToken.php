<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class QrRegistrationToken extends Model
{
    protected $table = 'qr_registration_tokens';

    protected $fillable = [
        'qr_campaign_id',
        'token_hash',
        'raw_token',
        'expires_at',
        'used_at',
        'used_by_user_id',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'raw_token' => 'encrypted',
            'expires_at' => 'datetime',
            'used_at' => 'datetime',
        ];
    }

    public function registrationUrl(): ?string
    {
        if (! $this->raw_token) {
            return null;
        }

        return route('senderista.register.create', ['token' => $this->raw_token]);
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(QrCampaign::class, 'qr_campaign_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function usedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'used_by_user_id');
    }

    public function senderistaRegistration(): HasOne
    {
        return $this->hasOne(SenderistaRegistration::class, 'qr_registration_token_id');
    }
}
