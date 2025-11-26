<?php

namespace Tests\Unit\Models;

use App\Models\Driver;
use App\Models\OfficialTrip;
use PHPUnit\Framework\TestCase; // Nota: Em testes unitários usamos este TestCase, não o do Laravel/Tests

class ModelLogicTest extends TestCase
{
    /**
     * Testa se o nome do motorista é formatado corretamente (Mutator).
     */
    public function test_driver_name_is_converted_to_title_case(): void
    {
        $driver = new Driver();
        $driver->name = 'joão da silva';

        // O mutator deve converter para 'João Da Silva'
        $this->assertEquals('João Da Silva', $driver->name);
    }

    /**
     * Testa o cálculo automático da distância (Accessor).
     */
    public function test_official_trip_calculates_distance_traveled(): void
    {
        $trip = new OfficialTrip();
        
        // Simulamos os atributos vindos da BD
        $trip->setRawAttributes([
            'departure_odometer' => 1000,
            'arrival_odometer' => 1250,
        ]);

        // O accessor 'distance_traveled' deve calcular a diferença
        $this->assertEquals(250, $trip->distance_traveled);
    }
    
    /**
     * Testa se a distância é 0 quando não há chegada.
     */
    public function test_official_trip_distance_is_zero_when_incomplete(): void
    {
        $trip = new OfficialTrip();
        $trip->setRawAttributes([
            'departure_odometer' => 1000,
            'arrival_odometer' => null,
        ]);

        $this->assertEquals(0, $trip->distance_traveled);
    }
}