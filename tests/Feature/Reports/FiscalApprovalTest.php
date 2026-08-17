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

    private User $fiscalUser;
    private ReportSubmission $submission;

    protected function setUp(): void
    {
        parent::setUp();

        $this->fiscalUser = User::factory()->fiscal('both')->create();

        $this->submission = ReportSubmission::create([
            'guard_id' => User::factory()->porteiro()->create()->id,
            'type' => 'private',
            'status' => 'pending',
            'start_date' => now()->subMonth()->startOfMonth(),
            'end_date' => now()->subMonth()->endOfMonth(),
            'submitted_at' => now(),
        ]);
    }

    public function test_fiscal_can_see_pending_submission_and_must_open_it_to_analyze(): void
    {
        Livewire::actingAs($this->fiscalUser)
            ->test(FiscalApproval::class)
            ->assertSee($this->submission->guardUser->name)
            ->assertSee('Analisar Relatório')
            ->assertDontSee('✓ Dar Visto');
    }

    public function test_requesting_approval_opens_confirmation_without_approving_yet(): void
    {
        Livewire::actingAs($this->fiscalUser)
            ->test(FiscalApproval::class)
            ->call('viewDetails', $this->submission->id)
            ->assertSet('isDetailsModalOpen', true)
            ->call('requestApproval', $this->submission->id)
            ->assertSet('isDetailsModalOpen', false)
            ->assertSet('isApprovalConfirmationOpen', true)
            ->assertSet('submissionPendingApprovalId', $this->submission->id);

        $this->assertDatabaseHas('report_submissions', [
            'id' => $this->submission->id,
            'status' => 'pending',
            'fiscal_id' => null,
        ]);
    }

    public function test_fiscal_can_confirm_approval_after_analyzing_report(): void
    {
        Livewire::actingAs($this->fiscalUser)
            ->test(FiscalApproval::class)
            ->call('viewDetails', $this->submission->id)
            ->call('requestApproval', $this->submission->id)
            ->call('confirmApproval')
            ->assertSet('isApprovalConfirmationOpen', false)
            ->assertSet('isDetailsModalOpen', false)
            ->assertSessionHas('success');

        $this->assertDatabaseHas('report_submissions', [
            'id' => $this->submission->id,
            'status' => 'approved',
            'fiscal_id' => $this->fiscalUser->id,
            'assigned_fiscal_id' => $this->fiscalUser->id,
        ]);

        $this->submission->refresh();
        $this->assertNotNull($this->submission->approved_at);
    }

    public function test_cannot_confirm_approval_without_confirmation_flow(): void
    {
        Livewire::actingAs($this->fiscalUser)
            ->test(FiscalApproval::class)
            ->call('confirmApproval')
            ->assertSessionHas('error');

        $this->assertDatabaseHas('report_submissions', [
            'id' => $this->submission->id,
            'status' => 'pending',
        ]);
    }

    public function test_private_fiscal_does_not_see_official_submission(): void
    {
        $privateFiscal = User::factory()->fiscal('private')->create();
        $officialSubmission = ReportSubmission::create([
            'guard_id' => User::factory()->porteiro()->create()->id,
            'type' => 'official',
            'status' => 'pending',
            'start_date' => now()->subMonth()->startOfMonth(),
            'end_date' => now()->subMonth()->endOfMonth(),
            'submitted_at' => now(),
        ]);

        Livewire::actingAs($privateFiscal)
            ->test(FiscalApproval::class)
            ->assertDontSee($officialSubmission->guardUser->name);
    }
}
