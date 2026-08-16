<?php

namespace Tests\Feature\Management;

use App\Livewire\DriverManagement;
use App\Models\User;
use App\Models\Driver;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class DriverManagementTest extends TestCase
{
    use RefreshDatabase;

    private $porteiroUser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->porteiroUser = User::factory()->porteiro()->create();
    }

    /**
     * Gera um CPF válido para testes.
     */
    private function generateValidCpf(): string
    {
        $n = [];
        for ($i = 0; $i < 9; $i++) {
            $n[] = rand(0, 9);
        }

        // Primeiro dígito
        $soma = 0;
        for ($i = 0; $i < 9; $i++) {
            $soma += $n[$i] * (10 - $i);
        }
        $resto = $soma % 11;
        $dv1 = ($resto < 2) ? 0 : 11 - $resto;
        $n[] = $dv1;

        // Segundo dígito
        $soma = 0;
        for ($i = 0; $i < 10; $i++) {
            $soma += $n[$i] * (11 - $i);
        }
        $resto = $soma % 11;
        $dv2 = ($resto < 2) ? 0 : 11 - $resto;
        $n[] = $dv2;

        return implode('', $n);
    }

    /**
     * Teste 1: Verifica se o componente carrega.
     */
    public function test_component_can_render(): void
    {
        Driver::factory()->create(['name' => 'Motorista Teste', 'is_authorized' => false]);

        Livewire::actingAs($this->porteiroUser)
            ->test(DriverManagement::class)
            ->assertStatus(200)
            ->assertSee('Gerenciamento de Motoristas');
    }

    /**
     * Teste 2: Verifica se o porteiro pode criar um motorista (NÃO autorizado).
     */
    public function test_porteiro_can_create_unauthorized_driver(): void
    {
        $validCpf = $this->generateValidCpf();

        Livewire::actingAs($this->porteiroUser)
            ->test(DriverManagement::class)
            ->set('name', 'Novo Motorista')
            ->set('document', $validCpf)
            ->set('telefone', '999999999') // Define um telefone explícito
            ->set('type', 'Visitante')
            ->set('is_authorized', false)
            ->call('store')
            ->assertHasNoErrors()
            ->assertSet('isModalOpen', false);

        $this->assertDatabaseHas('drivers', [
            'name' => 'Novo Motorista',
            'document' => $validCpf,
            'telefone' => '999999999', // Verifica se foi salvo
            'type' => 'Visitante',
            'is_authorized' => false,
        ]);
    }

    /**
     * Teste 3: Verifica validação de CPF duplicado.
     */
    public function test_cannot_create_driver_with_duplicate_document(): void
    {
        $validCpf = $this->generateValidCpf();

        // Cria o primeiro motorista com CPF válido
        Driver::factory()->create(['document' => $validCpf]);

        Livewire::actingAs($this->porteiroUser)
            ->test(DriverManagement::class)
            ->set('name', 'Outro Motorista')
            ->set('document', $validCpf) // Tenta usar o mesmo CPF
            ->set('type', 'Visitante')
            ->call('store')
            ->assertHasErrors(['document']); // A regra unique deve falhar
    }

    /**
     * Teste 4: Verifica se o porteiro pode editar um motorista NÃO autorizado.
     */
    public function test_porteiro_can_edit_unauthorized_driver(): void
    {
        // Cria motorista com CPF válido para evitar erro na edição
        $driver = Driver::factory()->create([
            'name' => 'Motorista Antigo',
            'document' => $this->generateValidCpf(),
            'is_authorized' => false,
        ]);

        Livewire::actingAs($this->porteiroUser)
            ->test(DriverManagement::class)
            ->call('edit', $driver->id)
            ->set('name', 'Motorista Editado')
            ->call('store')
            ->assertHasNoErrors()
            ->assertSet('isModalOpen', false);

        $this->assertDatabaseHas('drivers', [
            'id' => $driver->id,
            'name' => 'Motorista Editado',
        ]);
    }

    /**
     * Teste 5: Verifica se o porteiro NÃO pode editar um motorista AUTORIZADO.
     */
    public function test_porteiro_cannot_edit_authorized_driver(): void
    {
        $driver = Driver::factory()->create([
            'name' => 'Motorista Oficial',
            'document' => $this->generateValidCpf(),
            'is_authorized' => true,
        ]);

        Livewire::actingAs($this->porteiroUser)
            ->test(DriverManagement::class)
            ->call('edit', $driver->id)
            // Verifica que o modal não abriu (bloqueio no método edit)
            ->assertSet('isModalOpen', false);
    }

    /**
     * Teste 6: Verifica se o porteiro pode apagar (soft delete) um motorista NÃO autorizado.
     */
    public function test_porteiro_can_delete_unauthorized_driver(): void
    {
        $driver = Driver::factory()->create([
            'is_authorized' => false,
            'document' => $this->generateValidCpf()
        ]);

        Livewire::actingAs($this->porteiroUser)
            ->test(DriverManagement::class)
            ->call('confirmDelete', $driver->id)
            ->call('deleteDriver');

        $this->assertSoftDeleted('drivers', ['id' => $driver->id]);
    }
}
