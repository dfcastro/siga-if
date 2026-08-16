<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Auth; // Adicionar para a relação user, se necessário

class OfficialTrip extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'vehicle_id',
        'driver_id',
        'destination',
        'departure_datetime',
        'arrival_datetime',
        'departure_odometer',
        'arrival_odometer',
        'passengers',
        // 'guard_on_departure', // <-- REMOVER (ou comentar se a coluna ainda existe)
        // 'guard_on_arrival',   // <-- REMOVER (ou comentar se a coluna ainda existe)
        'guard_on_departure_id', // <-- ADICIONAR
        'guard_on_arrival_id',   // <-- ADICIONAR
        'return_observation',
        'distance_traveled', // Adicionado aqui para permitir atualização direta
        'report_submission_id' // Adicionar se ainda não estiver
        // 'user_id' // Se existir, adicionar aqui
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'departure_datetime' => 'datetime',
        'arrival_datetime' => 'datetime',
    ];

    /**
     * Get the vehicle associated with the trip.
     */
    public function vehicle(): BelongsTo
    {
        // Adicionar withTrashed() se quiser incluir veículos excluídos
        return $this->belongsTo(Vehicle::class)->withTrashed();
    }

    /**
     * Get the driver associated with the trip.
     */
    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class)->withTrashed();
    }

    /**
     * Get the user (guard) who registered the departure.
     */
    public function guardDeparture(): BelongsTo // <-- ADICIONAR RELAÇÃO
    {
        return $this->belongsTo(User::class, 'guard_on_departure_id');
    }

    /**
     * Get the user (guard) who registered the arrival.
     */
    public function guardArrival(): BelongsTo // <-- ADICIONAR RELAÇÃO
    {
        return $this->belongsTo(User::class, 'guard_on_arrival_id');
    }

    /**
     * Relação user() - Avaliar se ainda é necessária ou se deve ser renomeada/removida.
     * Se for para o criador do registo e não o porteiro, pode manter.
     * Exemplo: Se 'user_id' for preenchido automaticamente com Auth::id() ao criar.
     */
    // public function user(): BelongsTo
    // {
    //     return $this->belongsTo(User::class); // Presumindo que tem uma coluna user_id
    // }


    /**
     * Define um acessor/mutator para o atributo 'distance_traveled'.
     * O 'get' calcula a distância se não estiver definida.
     * O 'set' permite definir a distância diretamente (como no seu código de chegada).
     */
    protected function distanceTraveled(): Attribute
    {
        return Attribute::make(
            get: function ($value, $attributes) {
                // Se o valor já existe na BD (foi calculado na chegada), retorna-o
                if ($value !== null && is_numeric($value)) {
                    return (int)$value;
                }

                // Se não, tenta calcular na hora (útil para acessos antes da chegada ser salva)
                if (
                    isset($attributes['arrival_odometer']) && is_numeric($attributes['arrival_odometer']) &&
                    isset($attributes['departure_odometer']) && is_numeric($attributes['departure_odometer'])
                ) {
                    $distance = $attributes['arrival_odometer'] - $attributes['departure_odometer'];
                    return max(0, $distance); // Garante que não é negativo
                }
                return 0; // Retorna 0 se não for possível calcular
            },
            // Permite que defina a distância diretamente (ex: $trip->distance_traveled = $calculatedValue)
            set: fn ($value) => is_numeric($value) ? (int)$value : 0
        );
    }
}