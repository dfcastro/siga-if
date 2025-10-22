<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Driver;

class DriverFactory extends Factory
{
    protected $model = Driver::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'document' => $this->faker->unique()->numerify('###########'), // CPF Falso
            'type' => 'Servidor',
            'is_authorized' => true,
        ];
    }
}