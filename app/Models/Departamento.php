<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Departamento extends Model
{
    protected $table = 'departamentos';

    protected $fillable = [
        'nombre',
        'supervisor_id',
        'lider_id',
        'encargado_id',
    ];

    public function supervisor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }

    public function lider(): BelongsTo
    {
        return $this->belongsTo(User::class, 'lider_id');
    }

    public function encargado(): BelongsTo
    {
        return $this->belongsTo(User::class, 'encargado_id');
    }

    public function usuarios(): HasMany
    {
        return $this->hasMany(User::class, 'departamento_id');
    }

    public function finanzasMovimientos(): HasMany
    {
        return $this->hasMany(FinanzasMovimiento::class, 'departamento_id');
    }
}
