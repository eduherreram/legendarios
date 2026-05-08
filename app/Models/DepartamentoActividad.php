<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DepartamentoActividad extends Model
{
    protected $table = 'departamento_actividades';

    protected $fillable = [
        'departamento_id',
        'actividad_id',
        'estado',
        'pago_estado',
        'pago_monto_total',
        'pago_monto_pagado',
    ];

    public function departamento(): BelongsTo
    {
        return $this->belongsTo(Departamento::class, 'departamento_id');
    }

    public function actividad(): BelongsTo
    {
        return $this->belongsTo(Actividad::class, 'actividad_id');
    }

    public function asistencias(): HasMany
    {
        return $this->hasMany(Asistencia::class, 'departamento_actividad_id');
    }

    public function compromisos(): HasMany
    {
        return $this->hasMany(ActividadCompromiso::class, 'departamento_actividad_id');
    }
}
