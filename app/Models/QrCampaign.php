<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class QrCampaign extends Model
{
    protected $table = 'qr_campaigns';

    protected $fillable = [
        'nombre',
        'descripcion',
        'starts_at',
        'ends_at',
        'activa',
        'creado_por',
    ];

    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'activa' => 'boolean',
        ];
    }

    public function creadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creado_por');
    }

    public function tokens(): HasMany
    {
        return $this->hasMany(QrRegistrationToken::class, 'qr_campaign_id');
    }

    public function tribus(): HasMany
    {
        return $this->hasMany(Tribu::class, 'qr_campaign_id');
    }

    public function registrations(): HasManyThrough
    {
        return $this->hasManyThrough(
            SenderistaRegistration::class,
            QrRegistrationToken::class,
            'qr_campaign_id',
            'qr_registration_token_id',
            'id',
            'id',
        );
    }
}
