<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OfficialTrip extends Model
{
    use HasFactory;

    protected $guarded = [];

    // Converte automaticamente as colunas de data para objetos Carbon
    protected $casts = [
        'departure_datetime' => 'datetime',
        'arrival_datetime' => 'datetime',
    ];

    // Relacionamento: Uma viagem pertence a um Veículo
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    // Relacionamento: Uma viagem pertence a um Motorista
    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class);
    }
}