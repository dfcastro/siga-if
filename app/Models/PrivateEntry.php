<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // Importante adicionar esta linha

class PrivateEntry extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'entry_at' => 'datetime',
        'exit_at' => 'datetime',
    ];

    /**
     * Define o relacionamento: Uma entrada pertence a um Veículo.
     */
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    /**
     * Define o relacionamento: Uma entrada pertence a um Motorista (quem estava dirigindo).
     */
    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class);
    }
}