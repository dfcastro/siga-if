<?php

namespace Tests\Feature\Portaria;

use App\Livewire\CreatePrivateEntry;
use App\Livewire\PendingExits;
use App\Models\Driver;
use App\Models\PrivateEntry;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class PrivateEntryFlowTest extends TestCase
{
    use RefreshDatabase;

    private $porteiroUser;
    private $vehicle;
    private $driver;

    protected function setUp(): void
    {
        parent::setUp();
        $this->porteiroUser = User::factory()->porteiro()->create();

        $this->vehicle = Vehicle::factory()->create([
            'type' => 'Particular',
            'license_plate' => 'VIS-1234',
            'model' => 'Carro Visitante',
            'color' => 'Branco'
        ]);
        $this->driver = Driver::factory()->create([
            'type' => 'Visitante',
            'name' => 'João Visitante',
            'document' => '12345678900'
        ]);
    }

    /**
     * Teste 1: Registar uma entrada de veículo particular.
     */
    public function test_can_register_private_entry(): void
    {
        Livewire::actingAs($this->porteiroUser)
            ->test(CreatePrivateEntry::class)
            ->set('selected_driver_id', $this->driver->id)
            // CORREÇÃO: Chamar o método 'selectVehicle' para preencher placa e modelo
            ->call('selectVehicle', $this->vehicle->id)
            ->set('entry_reason', 'Reunião com a Direção')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('private_entries', [
            'driver_id' => $this->driver->id,
            'vehicle_id' => $this->vehicle->id,
            'entry_reason' => 'Reunião com a Direção',
            'guard_on_entry_id' => $this->porteiroUser->id,
            'exit_at' => null,
            'vehicle_model' => 'Carro Visitante',
            'license_plate' => 'VIS-1234',
        ]);
    }
    /**
     * Teste 2: Registar a saída de um veículo particular.
     */
    public function test_can_register_private_exit(): void
    {
        // 1. Criar uma entrada pendente ANTIGA (para aparecer na lista)
        $entry = PrivateEntry::create([
            'driver_id' => $this->driver->id,
            'vehicle_id' => $this->vehicle->id,
            'entry_at' => now()->subHours(13),
            'entry_reason' => 'Visita Técnica',
            'guard_on_entry_id' => $this->porteiroUser->id,
            'vehicle_model' => $this->vehicle->model,
            'license_plate' => $this->vehicle->license_plate,
            'vehicle_color' => $this->vehicle->color,
        ]);

        // 2. Registar saída
        Livewire::actingAs($this->porteiroUser)
            ->test(PendingExits::class)
            ->call('loadPendingData')
            ->assertSee('VIS-1234')
            ->call('confirmRegistration', $entry->id, 'private', 'exit')
            ->assertSet('isConfirmModalOpen', true)
            ->call('executeRegistration');

        $entry->refresh();
        $this->assertNotNull($entry->exit_at);
        $this->assertEquals($this->porteiroUser->id, $entry->guard_on_exit_id);
    }
}
