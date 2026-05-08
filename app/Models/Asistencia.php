<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Asistencia extends Model
{
    protected $table = 'asistencias';

    protected $fillable = [
        'departamento_actividad_id',
        'actividad_compromiso_id',
        'user_id',
        'estado',
        'pago_estado',
        'pago_monto_pagado',
        'pago_registrado_en',
        'pago_registrado_por',
        'marcada_en',
        'marcada_por',
    ];

    protected function casts(): array
    {
        return [
            'marcada_en' => 'datetime',
            'pago_registrado_en' => 'datetime',
        ];
    }

    public function departamentoActividad(): BelongsTo
    {
        return $this->belongsTo(DepartamentoActividad::class, 'departamento_actividad_id');
    }

    public function compromiso(): BelongsTo
    {
        return $this->belongsTo(ActividadCompromiso::class, 'actividad_compromiso_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function marcadaPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'marcada_por');
    }

    public function pagoRegistradoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pago_registrado_por');
    }
}
