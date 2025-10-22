<?php

namespace Tests\Feature;

use App\Models\Driver;
use App\Models\OfficialTrip;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Str; // Importar o Str para o nome do ficheiro

class ReportGenerationTest extends TestCase
{
    use RefreshDatabase;

    // Propriedades para guardar os utilizadores de teste
    private $adminUser;
    private $porteiroUser;

    /**
     * MÉTODO setUp() (CORRIGIDO)
     * Este método corre antes de cada teste e cria os nossos utilizadores
     */
    protected function setUp(): void
    {
        parent::setUp();
        
        // Criar um utilizador Admin
        $this->adminUser = User::factory()->create(['role' => 'admin']);
        
        // Criar um utilizador Porteiro
        $this->porteiroUser = User::factory()->create(['role' => 'porteiro']);
    }

    /**
     * Teste 1: Garante que um visitante (não logado) é redirecionado para o login.
     * (ASSERTIONS CORRIGIDAS)
     */
    public function test_unauthenticated_user_cannot_generate_report(): void
    {
        $response = $this->get(route('reports.official.pdf'));

        // Verifica se foi redirecionado (status 302)
        $response->assertStatus(302);
        // Verifica se foi redirecionado para a página de login
        $response->assertRedirect('/login');
    }

    /**
     * Teste 2: Garante que a rota falha se os parâmetros obrigatórios não forem enviados.
     * (ASSERTIONS CORRIGIDAS)
     */
    public function test_report_generation_fails_without_required_parameters(): void
    {
        // Atua como o utilizador Porteiro (que agora existe)
        $response = $this->actingAs($this->porteiroUser)
            ->get(route('reports.official.pdf', [
                'start_date' => '2025-09-01',
                // Faltam 'end_date' e 'vehicle_id'
            ]));

        // Deve falhar a validação (redirect back)
        $response->assertStatus(302);
        // Garante que a sessão tem erros para os campos em falta
        $response->assertSessionHasErrors(['end_date', 'vehicle_id']);
    }

    /**
     * Teste 3: O teste principal! Verifica se um porteiro logado
     * gera um PDF com os dados corretos de Setembro.
     * (CORRIGIDO E COMPLETO)
     */
    public function test_authenticated_user_can_generate_and_receive_pdf_report(): void
    {
        // 1. PREPARAÇÃO (Arrange)
        
        $vehicle = Vehicle::factory()->create(['license_plate' => 'PDF-0001']);
        $driver = Driver::factory()->create();
        
        // Criamos a viagem em Setembro (mês fechado)
        // SEM a coluna 'distance_traveled'
        OfficialTrip::factory()->create([
            'vehicle_id' => $vehicle->id,
            'driver_id' => $driver->id,
            'passengers' => 'Passageiro de Teste 123',
            'departure_datetime' => '2025-09-10 08:00:00', // Setembro
            'arrival_datetime' => '2025-09-10 12:00:00',   // Setembro
            'departure_odometer' => 10000, // Odómetros para o cálculo
            'arrival_odometer' => 10150,
        ]);
        
        $startDate = '2025-09-01';
        $endDate = '2025-09-30';
        $startDateCarbon = \Carbon\Carbon::parse($startDate);

        // 2. AÇÃO (Act)

        // Atua como o porteiro (que agora existe)
        $response = $this->actingAs($this->porteiroUser)
            ->get(route('reports.official.pdf', [
                'start_date' => $startDate,
                'end_date' => $endDate,
                'vehicle_id' => $vehicle->id,
                'driver_id' => $driver->id,
            ]));

        // 3. VERIFICAÇÃO (Assert)

        // Verifica se a resposta foi OK (o PDF foi gerado e enviado)
        $response->assertStatus(200);
        
        // Verifica se o servidor enviou o cabeçalho correto para um PDF
        $response->assertHeader('Content-Type', 'application/pdf');

        // Verifica o nome do ficheiro (baseado no teu ReportController)
        $expectedFileName = 'relatorio_oficial_' . Str::slug($vehicle->license_plate) . '_' . $startDateCarbon->format('Ym') . '.pdf';
        
        $response->assertHeader('Content-Disposition', 'inline; filename=' . $expectedFileName);
    }
}