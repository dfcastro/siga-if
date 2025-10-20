<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Casts\Attribute; // <-- IMPORTAR CLASSE DE ATRIBUTO

class OfficialTrip extends Model
{
    use HasFactory;

    // A sua lista de fillable está correta
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
        'return_observation', // Garanta que este campo esteja no fillable também
        
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
        return $this->belongsTo(Driver::class)->withTrashed();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * CORREÇÃO APLICADA AQUI
     *
     * Define um acessor para o atributo 'distance_traveled'.
     * Este código será executado automaticamente sempre que você tentar aceder a $trip->distance_traveled.
     */
    protected function distanceTraveled(): Attribute
    {
        return Attribute::make(
            get: function ($value, $attributes) {
                // Verifica se os odómetros de chegada e saída existem e são numéricos
                if (
                    isset($attributes['arrival_odometer']) && is_numeric($attributes['arrival_odometer']) &&
                    isset($attributes['departure_odometer']) && is_numeric($attributes['departure_odometer'])
                ) {
                    // Calcula a diferença
                    return $attributes['arrival_odometer'] - $attributes['departure_odometer'];
                }

                // Se algum dos valores não for válido, retorna 0
                return 0;
            }
        );
    }
}
