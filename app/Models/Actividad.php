<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Actividad extends Model
{
    protected $table = 'actividades';

    protected $fillable = [
        'nombre',
        'fecha',
        'precio',
        'registration_token_hash',
        'raw_registration_token',
        'registration_expires_at',
        'estado',
        'cerrada_en',
        'cerrada_por',
    ];

    protected function casts(): array
    {
        return [
            'fecha' => 'date',
            'raw_registration_token' => 'encrypted',
            'registration_expires_at' => 'datetime',
            'cerrada_en' => 'datetime',
        ];
    }

    public function departamentoActividades(): HasMany
    {
        return $this->hasMany(DepartamentoActividad::class, 'actividad_id');
    }

    public function compromisos(): HasMany
    {
        return $this->hasMany(ActividadCompromiso::class, 'actividad_id');
    }

    public function registrationUrl(): ?string
    {
        if (! $this->raw_registration_token) {
            return null;
        }

        return route('actividad.register.create', ['token' => $this->raw_registration_token]);
    }
}
