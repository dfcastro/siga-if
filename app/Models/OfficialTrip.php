<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OfficialTrip extends Model
{
    use HasFactory;

    // CORREÇÃO APLICADA AQUI
    protected $fillable = [
        'vehicle_id',
        'driver_id',
        'destination',
        'departure_datetime',
        'arrival_datetime',
        'departure_odometer',
        'arrival_odometer',
        'passengers',
        'guard_on_departure', 
        'guard_on_arrival',   
    ];

    protected $casts = [
        'departure_datetime' => 'datetime',
        'arrival_datetime' => 'datetime',
    ];

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class);
    }
//  Define a relação com o Utilizador (Porteiro) que registou a viagem.
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
