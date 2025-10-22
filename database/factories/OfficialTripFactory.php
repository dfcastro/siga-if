<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\OfficialTrip;
use App\Models\Vehicle;
use App\Models\Driver;
use App\Models\User;

class OfficialTripFactory extends Factory
{
    protected $model = OfficialTrip::class;

    public function definition(): array
    {
        $departureKm = $this->faker->numberBetween(10000, 50000);
        $arrivalKm = $departureKm + $this->faker->numberBetween(100, 500);

        return [
            'vehicle_id' => Vehicle::factory(),
            'driver_id' => Driver::factory(),
            'destination' => 'Cidade de Teste',
            'passengers' => 'Passageiro A, Passageiro B',
            'departure_datetime' => now()->subDays(1),
            'departure_odometer' => $departureKm,
            'arrival_datetime' => now(),
            'arrival_odometer' => $arrivalKm,
            // 'distance_traveled' => $arrivalKm - $departureKm, // <-- REMOVE ESTA LINHA
            'return_observation' => 'Tudo certo no teste.',
            'guard_on_departure_id' => User::factory(), 
            'guard_on_arrival_id' => User::factory(),   
        ];
    }
}