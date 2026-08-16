<?php

namespace Tests\Feature\Management;

use App\Livewire\VehicleManagement;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class VehicleManagementTest extends TestCase
{
    use RefreshDatabase;

    private $porteiroUser;

    protected function setUp(): void
    {
        parent::setUp();
        // Usar o porteiro, pois ele tem acesso a veículos particulares
        $this->porteiroUser = User::factory()->porteiro()->create();
    }

    /**
     * Teste 1: Verifica se o componente carrega.
     */
    public function test_component_can_render(): void
    {
        // Criar um veículo Particular para que a lista não esteja vazia
        Vehicle::factory()->create(['type' => 'Particular', 'license_plate' => 'ABC-1234']);

        Livewire::actingAs($this->porteiroUser)
            ->test(VehicleManagement::class)
            ->assertStatus(200)
            ->assertSee('Gerenciamento de Veículos')
            ->assertSee('ABC-1234');
    }

    /**
     * Teste 2: Verifica se é possível criar um novo veículo Particular.
     */
    public function test_can_create_new_particular_vehicle(): void
    {
        Livewire::actingAs($this->porteiroUser)
            ->test(VehicleManagement::class)
            ->set('license_plate', 'NEW-1234')
            ->set('model', 'Fiat Uno Novo')
            ->set('color', 'Vermelho')
            ->set('type', 'Particular')
            ->call('store')
            ->assertHasNoErrors()
            ->assertSet('isModalOpen', false); // CORRIGIDO: Verifica o estado em vez do evento

        $this->assertDatabaseHas('vehicles', [
            'license_plate' => 'NEW-1234',
            'model' => 'FIAT UNO NOVO',
            'type' => 'Particular',
        ]);
    }

    /**
     * Teste 3: Verifica validação de placa duplicada.
     */
    public function test_cannot_create_vehicle_with_duplicate_license_plate(): void
    {
        Vehicle::factory()->create(['license_plate' => 'DUP-1111', 'type' => 'Particular']);

        Livewire::actingAs($this->porteiroUser)
            ->test(VehicleManagement::class)
            ->set('license_plate', 'DUP-1111')
            ->set('model', 'Modelo Qualquer')
            ->set('color', 'Azul')
            ->set('type', 'Particular')
            ->call('store')
            ->assertHasErrors(['license_plate' => 'unique']);
    }

    /**
     * Teste 4: Verifica se é possível editar um veículo Particular existente.
     */
    public function test_can_edit_particular_vehicle(): void
    {
        $vehicle = Vehicle::factory()->create([
            'license_plate' => 'OLD-0001',
            'model' => 'Modelo Antigo',
            'color' => 'Preto',
            'type' => 'Particular',
        ]);

        Livewire::actingAs($this->porteiroUser)
            ->test(VehicleManagement::class)
            ->call('edit', $vehicle->id)
            ->set('license_plate', 'EDT-9999')
            ->set('model', 'Modelo Editado')
            ->set('color', 'Branco')
            ->set('type', 'Particular')
            ->call('store')
            ->assertHasNoErrors()
            ->assertSet('isModalOpen', false); // CORRIGIDO: Verifica o estado em vez do evento

        $this->assertDatabaseHas('vehicles', [
            'id' => $vehicle->id,
            'license_plate' => 'EDT-9999',
            'model' => 'MODELO EDITADO',
            'color' => 'BRANCO',
        ]);
        $this->assertDatabaseMissing('vehicles', ['license_plate' => 'OLD-0001']);
    }

    /**
     * Teste 5: Verifica se é possível apagar (soft delete) um veículo Particular.
     */
    public function test_can_delete_particular_vehicle(): void
    {
        $vehicle = Vehicle::factory()->create(['type' => 'Particular']);

        Livewire::actingAs($this->porteiroUser)
            ->test(VehicleManagement::class)
            ->call('confirmDelete', $vehicle->id)
            ->call('deleteVehicle');

        $this->assertSoftDeleted('vehicles', ['id' => $vehicle->id]);
    }

    /**
     * Teste 6: Porteiro não pode criar veículo Oficial.
     */
    public function test_porteiro_cannot_create_official_vehicle(): void
    {
        Livewire::actingAs($this->porteiroUser)
            ->test(VehicleManagement::class)
            ->set('license_plate', 'OFC-5678')
            ->set('model', 'Veiculo Oficial')
            ->set('color', 'Verde')
            ->set('type', 'Oficial')
            ->call('store')
            ->assertHasNoErrors(); // Garante que não foi erro de validação comum

        $this->assertDatabaseMissing('vehicles', [
            'license_plate' => 'OFC-5678',
            'type' => 'Oficial',
        ]);
    }
}
