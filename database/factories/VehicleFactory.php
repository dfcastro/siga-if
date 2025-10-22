<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Vehicle;

class VehicleFactory extends Factory
{
    protected $model = Vehicle::class;

    public function definition(): array
    {
        return [
            'license_plate' => $this->faker->unique()->regexify('[A-Z]{3}-[0-9]{4}'),
            'model' => 'Veículo de Teste',
            'color' => 'Prata',
            // 'owner_name' => 'IFNMG', // <-- REMOVE ESTA LINHA
            'type' => 'official', 
        ];
    }
}