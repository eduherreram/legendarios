<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reconocimiento extends Model
{
    protected $table = 'reconocimientos';

    protected $fillable = [
        'nombre_reconocimiento',
        'descripcion',
        'motivo',
        'numero_legendario',
        'nombre',
        'apellido',
        'imagen_path',
        'user_id',
        'enviar_email',
        'enviar_whatsapp',
        'email_enviado_at',
        'whatsapp_preparado_at',
        'creado_por',
    ];

    protected function casts(): array
    {
        return [
            'enviar_email' => 'boolean',
            'enviar_whatsapp' => 'boolean',
            'email_enviado_at' => 'datetime',
            'whatsapp_preparado_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function creadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creado_por');
    }
}
