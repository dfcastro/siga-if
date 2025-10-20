<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Vehicle;
use App\Models\ReportSubmission;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class ReportStatus extends Component
{
    public $months;
    public $submissions;
    public $vehicles;
    public string $reportType = 'official';

    public function layoutData()
    {
        return ['header' => 'Status de Submissão de Relatórios'];
    }

    /**
     * Prepara os dados iniciais, respeitando o perfil do utilizador.
     */
    public function mount()
    {
        $user = Auth::user();

        // Define a aba padrão com base no perfil do fiscal
        if ($user->role === 'fiscal') {
            if ($user->fiscal_type === 'private') {
                $this->reportType = 'private';
            } else {
                // 'official' e 'both' começam na aba oficial por padrão
                $this->reportType = 'official';
            }
        }

        $this->loadReportData();
    }

    /**
     * Recarrega os dados quando o utilizador troca de aba.
     */
    public function updatedReportType()
    {
        $this->loadReportData();
    }

    /**
     * Carrega os dados corretos para a aba selecionada, aplicando as regras de permissão.
     */
    public function loadReportData()
    {
        $user = Auth::user();

        $this->months = collect(range(0, 11))->map(fn($i) => Carbon::now()->subMonths($i))->reverse();
        $query = ReportSubmission::where('start_date', '>=', $this->months->first()->copy()->startOfMonth());

        if ($this->reportType === 'official') {
            $this->handleOfficialReports($user, $query);
        } else {
            $this->handlePrivateReports($user, $query);
        }
    }

    private function handleOfficialReports($user, $query)
    {
        // Se for um fiscal sem permissão, retorna vazio
        if ($user->role === 'fiscal' && !in_array($user->fiscal_type, ['official', 'both'])) {
            $this->vehicles = collect();
            $this->submissions = collect();
            return;
        }

        $this->vehicles = Vehicle::where('type', 'Oficial')->orderBy('model')->get();
        $this->submissions = $query->where('type', 'official')
            ->get()
            ->groupBy('vehicle_id')
            ->map(fn($group) => $group->keyBy(fn($item) => Carbon::parse($item->start_date)->format('Y-m')));
    }

    private function handlePrivateReports($user, $query)
    {
        // Se for um fiscal sem permissão, retorna vazio
        if ($user->role === 'fiscal' && !in_array($user->fiscal_type, ['private', 'both'])) {
            $this->submissions = collect();
            return;
        }

        $this->vehicles = null; // Não aplicável para a visão de relatórios particulares

        // Se for porteiro, vê apenas os seus próprios relatórios
        if ($user->role === 'porteiro') {
            $query->where('guard_id', $user->id);
        }
        // Fiscais e Admins veem todos os relatórios particulares

        $this->submissions = $query->where('type', 'private')
            ->get()
            ->keyBy(fn($item) => Carbon::parse($item->start_date)->format('Y-m'));
    }

    public function render()
    {
        return view('livewire.report-status');
    }
}
