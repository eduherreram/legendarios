<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

#[Fillable([
    'numero_legendario',
    'nombre',
    'apellido',
    'rut',
    'fecha_nacimiento',
    'enfermedad',
    'talla',
    'iglesia',
    'es_pastor',
    'direccion_calle',
    'direccion_numero',
    'direccion_comuna',
    'direccion_region',
    'direccion_pais',
    'telefono',
    'email',
    'password',
    'nombre_contacto_emergencia',
    'parentesco_contacto_emergencia',
    'telefono_contacto_emergencia',
    'estado',
    'fecha_inscripcion',
    'departamento_id',
])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;
    use HasRoles;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'fecha_nacimiento' => 'date',
            'enfermedad' => 'boolean',
            'es_pastor' => 'boolean',
            'fecha_inscripcion' => 'datetime',
        ];
    }

    public function getNameAttribute(): string
    {
        return trim(($this->nombre ?? '').' '.($this->apellido ?? ''));
    }

    public function departamento(): BelongsTo
    {
        return $this->belongsTo(Departamento::class, 'departamento_id');
    }

    public function senderistaRegistration(): HasOne
    {
        return $this->hasOne(SenderistaRegistration::class);
    }
}
