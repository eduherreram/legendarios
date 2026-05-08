<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class SenderistaRegistration extends Model
{
    protected $table = 'senderista_registrations';

    protected $fillable = [
        'user_id',
        'qr_registration_token_id',
        'registered_at',
    ];

    protected function casts(): array
    {
        return [
            'registered_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function token(): BelongsTo
    {
        return $this->belongsTo(QrRegistrationToken::class, 'qr_registration_token_id');
    }

    public function tribuMember(): HasOne
    {
        return $this->hasOne(TribuMember::class, 'senderista_registration_id');
    }
}
