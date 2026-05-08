<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FinanzasMovimiento extends Model
{
    protected $table = 'finanzas_movimientos';

    protected $fillable = [
        'tipo',
        'monto',
        'descripcion',
        'departamento_id',
        'departamento_actividad_id',
        'fecha',
        'creado_por',
    ];

    protected function casts(): array
    {
        return [
            'fecha' => 'date',
        ];
    }

    public function departamento(): BelongsTo
    {
        return $this->belongsTo(Departamento::class, 'departamento_id');
    }

    public function departamentoActividad(): BelongsTo
    {
        return $this->belongsTo(DepartamentoActividad::class, 'departamento_actividad_id');
    }

    public function creadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creado_por');
    }
}
