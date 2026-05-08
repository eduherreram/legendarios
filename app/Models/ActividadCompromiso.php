<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ActividadCompromiso extends Model
{
    protected $table = 'actividad_compromisos';

    protected $fillable = [
        'actividad_id',
        'departamento_actividad_id',
        'user_id',
        'registered_at',
    ];

    protected function casts(): array
    {
        return [
            'registered_at' => 'datetime',
        ];
    }

    public function actividad(): BelongsTo
    {
        return $this->belongsTo(Actividad::class, 'actividad_id');
    }

    public function departamentoActividad(): BelongsTo
    {
        return $this->belongsTo(DepartamentoActividad::class, 'departamento_actividad_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function asistencia(): HasOne
    {
        return $this->hasOne(Asistencia::class, 'actividad_compromiso_id');
    }
}
