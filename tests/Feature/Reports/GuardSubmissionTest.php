<?php

namespace Tests\Feature\Reports;

use App\Livewire\GuardReport;
use App\Models\OfficialTrip;
use App\Models\PrivateEntry;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\Driver;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;
use Carbon\Carbon;

class GuardSubmissionTest extends TestCase
{
    use RefreshDatabase;

    private $porteiroUser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->porteiroUser = User::factory()->porteiro()->create();
        // Criar um fiscal para ser atribuído automaticamente
        User::factory()->fiscal('both')->create();
    }

    /**
     * Teste 1: Submeter relatório de particulares do mês passado.
     */
    public function test_can_submit_private_report_for_previous_month(): void
    {
        // Data no mês passado
        $lastMonth = Carbon::now()->subMonth();
        
        // Criar entrada finalizada no mês passado
        PrivateEntry::create([
            'driver_id' => Driver::factory()->create()->id,
            'vehicle_id' => Vehicle::factory()->create()->id,
            'guard_on_entry_id' => $this->porteiroUser->id,
            'guard_on_exit_id' => $this->porteiroUser->id,
            'entry_at' => $lastMonth->copy()->startOfMonth()->addDay(),
            'exit_at' => $lastMonth->copy()->startOfMonth()->addDay()->addHour(),
            'entry_reason' => 'Teste',
            'vehicle_model' => 'Teste',
            'license_plate' => 'TST-1234',
            'vehicle_color' => 'Branco',
        ]);

        Livewire::actingAs($this->porteiroUser)
            ->test(GuardReport::class)
            ->set('submissionType', 'private')
            ->set('reportMonth', $lastMonth->format('Y-m')) // Seleciona mês passado
            ->call('confirmSubmission', 'private') // Abre confirmação
            ->assertSet('showConfirmationModal', true)
            ->call('executeConfirmedAction'); // Executa submitPrivateReport

        // Verifica se a submissão foi criada
        $this->assertDatabaseHas('report_submissions', [
            'guard_id' => $this->porteiroUser->id,
            'type' => 'private',
            'status' => 'pending',
            'start_date' => $lastMonth->copy()->startOfMonth()->toDateTimeString(),
        ]);
    }

    /**
     * Teste 2: Não pode submeter mês atual.
     */
    public function test_cannot_submit_current_month(): void
    {
        Livewire::actingAs($this->porteiroUser)
            ->test(GuardReport::class)
            ->set('reportMonth', Carbon::now()->format('Y-m'))
            ->call('submitPrivateReport') // Tenta submeter direto (o método valida)
            // O método deve falhar silenciosamente ou mostrar erro flash, 
            // mas NÃO deve criar registo
            ->assertSet('showConfirmationModal', false); 

        $this->assertDatabaseCount('report_submissions', 0);
    }
}