<?php

namespace Tests\Feature\Portaria;

use App\Livewire\OfficialFleetManagement;
use App\Models\Driver;
use App\Models\OfficialTrip;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class OfficialTripFlowTest extends TestCase
{
    use RefreshDatabase;

    private $porteiroUser;
    private $vehicle;
    private $driver;

    protected function setUp(): void
    {
        parent::setUp();
        $this->porteiroUser = User::factory()->porteiro()->create();
        
        // Criar veículo oficial e motorista autorizado
        $this->vehicle = Vehicle::factory()->create(['type' => 'official', 'model' => 'Veículo Oficial Teste']);
        $this->driver = Driver::factory()->create(['is_authorized' => true, 'name' => 'Motorista Oficial']);
    }

    /**
     * Teste 1: Registar uma saída de veículo oficial com sucesso.
     */
    public function test_can_register_official_vehicle_departure(): void
    {
        Livewire::actingAs($this->porteiroUser)
            ->test(OfficialFleetManagement::class)
            ->call('create') // Abre o modal de saída
            ->set('vehicle_id', $this->vehicle->id)
            // Ao definir o veículo, o componente busca o lastOdometer (que deve ser 0 ou null)
            ->set('driver_id', $this->driver->id)
            ->set('destination', 'Prefeitura')
            ->set('departure_odometer', 1000)
            ->call('storeDeparture')
            ->assertHasNoErrors()
            ->assertSet('isDepartureModalOpen', false);

        // Verifica se a viagem foi criada (com chegada nula)
        $this->assertDatabaseHas('official_trips', [
            'vehicle_id' => $this->vehicle->id,
            'driver_id' => $this->driver->id,
            'destination' => 'Prefeitura',
            'departure_odometer' => 1000,
            'arrival_datetime' => null,
            'guard_on_departure_id' => $this->porteiroUser->id,
        ]);
    }

    /**
     * Teste 2: Não pode registar saída com KM menor que a última chegada.
     */
    public function test_cannot_register_departure_with_lower_odometer(): void
    {
        // Simula uma viagem anterior que terminou com KM 2000
        OfficialTrip::factory()->create([
            'vehicle_id' => $this->vehicle->id,
            'driver_id' => $this->driver->id,
            'departure_odometer' => 1000,
            'arrival_odometer' => 2000,
            'arrival_datetime' => now()->subHour(),
        ]);

        Livewire::actingAs($this->porteiroUser)
            ->test(OfficialFleetManagement::class)
            ->call('create')
            ->set('vehicle_id', $this->vehicle->id)
            // O componente deve detetar que o último KM foi 2000
            ->assertSet('lastOdometer', 2000) 
            ->set('driver_id', $this->driver->id)
            ->set('destination', 'Viagem Inválida')
            ->set('departure_odometer', 1500) // Menor que 2000
            ->call('storeDeparture')
            ->assertHasErrors(['departure_odometer']);
    }

    /**
     * Teste 3: Registar a chegada de um veículo oficial.
     */
    public function test_can_register_official_vehicle_arrival(): void
    {
        // Cria uma viagem em andamento (sem chegada)
        $trip = OfficialTrip::factory()->create([
            'vehicle_id' => $this->vehicle->id,
            'driver_id' => $this->driver->id,
            'departure_odometer' => 1000,
            'arrival_datetime' => null,
            'arrival_odometer' => null,
        ]);

        Livewire::actingAs($this->porteiroUser)
            ->test(OfficialFleetManagement::class)
            ->call('openArrivalModal', $trip->id)
            ->set('arrival_odometer', 1200)
            ->set('return_observation', 'Tudo ok')
            ->call('storeArrival')
            ->assertHasNoErrors()
            ->assertSet('isArrivalModalOpen', false);

        // Verifica se a viagem foi atualizada
        $this->assertDatabaseHas('official_trips', [
            'id' => $trip->id,
            'arrival_odometer' => 1200,
            'return_observation' => 'Tudo ok',
            'guard_on_arrival_id' => $this->porteiroUser->id,
        ]);
        
        // Verifica que arrival_datetime não é mais nulo
        $trip->refresh();
        $this->assertNotNull($trip->arrival_datetime);
    }

    /**
     * Teste 4: Não pode registar chegada com KM menor que a saída.
     */
    public function test_cannot_register_arrival_with_invalid_odometer(): void
    {
        $trip = OfficialTrip::factory()->create([
            'vehicle_id' => $this->vehicle->id,
            'driver_id' => $this->driver->id,
            'departure_odometer' => 1000,
            'arrival_datetime' => null,
        ]);

        Livewire::actingAs($this->porteiroUser)
            ->test(OfficialFleetManagement::class)
            ->call('openArrivalModal', $trip->id)
            ->set('arrival_odometer', 900) // Menor que a saída (1000)
            ->call('storeArrival')
            ->assertHasErrors(['arrival_odometer' => 'gt']); // Regra 'gt' (greater than)
    }
}