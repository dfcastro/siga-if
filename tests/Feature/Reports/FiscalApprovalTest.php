<?php

namespace Tests\Feature\Reports;

use App\Livewire\FiscalApproval;
use App\Models\ReportSubmission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class FiscalApprovalTest extends TestCase
{
    use RefreshDatabase;

    private $fiscalUser;
    private $submission;

    protected function setUp(): void
    {
        parent::setUp();
        $this->fiscalUser = User::factory()->fiscal('both')->create();
        
        // Criar uma submissão pendente
        $this->submission = ReportSubmission::create([
            'guard_id' => User::factory()->porteiro()->create()->id,
            'type' => 'private',
            'status' => 'pending',
            'start_date' => now()->subMonth()->startOfMonth(),
            'end_date' => now()->subMonth()->endOfMonth(),
            'submitted_at' => now(),
        ]);
    }

    /**
     * Teste 1: Fiscal vê a submissão pendente.
     */
    public function test_fiscal_can_see_pending_submission(): void
    {
        Livewire::actingAs($this->fiscalUser)
            ->test(FiscalApproval::class)
            ->assertSee($this->submission->guardUser->name); // Vê o nome do porteiro
    }

    /**
     * Teste 2: Fiscal aprova a submissão.
     */
    public function test_fiscal_can_approve_submission(): void
    {
        Livewire::actingAs($this->fiscalUser)
            ->test(FiscalApproval::class)
            ->call('viewSubmission', $this->submission->id) // Abre modal de detalhes
            ->assertSet('showDetailsModal', true)
            ->call('approveSubmission');

        // Verifica atualização na base de dados
        $this->assertDatabaseHas('report_submissions', [
            'id' => $this->submission->id,
            'status' => 'approved',
            'fiscal_id' => $this->fiscalUser->id, // Fiscal que aprovou
        ]);
        
        $this->submission->refresh();
        $this->assertNotNull($this->submission->approved_at);
    }
}