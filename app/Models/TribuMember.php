<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TribuMember extends Model
{
    protected $table = 'tribu_members';

    protected $fillable = [
        'tribu_id',
        'senderista_registration_id',
        'assigned_by',
        'assigned_at',
    ];

    protected function casts(): array
    {
        return [
            'assigned_at' => 'datetime',
        ];
    }

    public function tribu(): BelongsTo
    {
        return $this->belongsTo(Tribu::class, 'tribu_id');
    }

    public function registration(): BelongsTo
    {
        return $this->belongsTo(SenderistaRegistration::class, 'senderista_registration_id');
    }

    public function assignedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }
}
