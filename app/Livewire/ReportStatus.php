<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Component;
use App\Models\Vehicle;
use App\Models\ReportSubmission;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB; // ### ADICIONAR IMPORT ###
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Config;  // Manter

#[Layout('layouts.app')]
class ReportStatus extends Component
{
    public $months;
    public $submissions;
    public $vehicles;
    public string $reportType = 'official';
    public $porteiros;

    // ### INÍCIO DAS NOVAS PROPRIEDADES ###
    public $selectedYear;
    public $availableYears = [];
    // ### FIM DAS NOVAS PROPRIEDADES ###

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

        // Determina a expressão SQL correta para extrair o ano
        $databaseConnection = Config::get('database.default');
        $driver = Config::get("database.connections.{$databaseConnection}.driver");

        if ($driver === 'sqlite') {
            $minYearQuery = DB::raw("CAST(strftime('%Y', start_date) AS INTEGER)");
        } else {
            $minYearQuery = DB::raw("YEAR(start_date)");
        }

        $firstYear = ReportSubmission::min($minYearQuery);
        $currentYear = Carbon::now()->year;

        if (!$firstYear) {
            $firstYear = $currentYear;
        }

        $this->availableYears = range($currentYear, (int)$firstYear);
        $this->selectedYear = $currentYear;

        // Define a aba padrão respeitando o perfil
        if ($user->role === 'fiscal') {
            if ($user->fiscal_type === 'private') {
                $this->reportType = 'private';
            } else {
                $this->reportType = 'official';
            }
        } else {
            // Admin e Porteiro começam na aba Oficial por padrão
            $this->reportType = 'official';
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

    // ### INÍCIO - NOVO MÉTODO ###
    /**
     * Recarrega os dados quando o utilizador troca o ano.
     */
    public function updatedSelectedYear()
    {
        $this->loadReportData();
    }
    // ### FIM - NOVO MÉTODO ###


    /**
     * Carrega os dados corretos para a aba selecionada, aplicando as regras de permissão.
     */
    public function loadReportData()
    {
        $user = Auth::user();

        // ### ALTERAÇÃO NA LÓGICA DE DADOS ###

        // 1. Gera os 12 meses do ano selecionado (Jan/Ano, Fev/Ano, ...)
        $this->months = collect(range(1, 12))->map(fn($month) => Carbon::create((int)$this->selectedYear, $month, 1));

        // 2. Cria a consulta base filtrando pelo ano selecionado
        $query = ReportSubmission::whereYear('start_date', $this->selectedYear);

        // ### FIM DA ALTERAÇÃO ###

        // Resetar dados (lógica mantida)
        $this->vehicles = collect();
        $this->porteiros = collect();
        $this->submissions = collect();

        if ($this->reportType === 'official') {
            $this->handleOfficialReports($user, $query);
        } else {
            $this->handlePrivateReports($user, $query);
        }
    }

    private function handleOfficialReports($user, $query)
    {
        // Se for fiscal particular, ele não vê a aba oficial
        if ($user->role === 'fiscal' && !in_array($user->fiscal_type, ['official', 'both'])) {
            $this->vehicles = collect();
            $this->submissions = collect();
            return;
        }

        // Carrega todos os veículos oficiais para montar a grade
        $this->vehicles = Vehicle::where('type', 'Oficial')->orderBy('model')->get();

        $officialQuery = clone $query;

        // SE FOR PORTEIRO: filtra apenas as submissões feitas por ELE!
        if ($user->role === 'porteiro') {
            $officialQuery->where('guard_id', $user->id);
        }

        $this->submissions = $officialQuery->where('type', 'official')
            ->get()
            ->groupBy('vehicle_id')
            ->map(fn($group) => $group->keyBy(fn($item) => Carbon::parse($item->start_date)->format('Y-m')));
    }
    private function handlePrivateReports($user, $query)
    {
        // (Lógica interna mantida - o $query já está filtrado por ano)
        if ($user->role === 'fiscal' && !in_array($user->fiscal_type, ['private', 'both'])) {
            $this->submissions = collect();
            return;
        }

        // Clona a query
        $privateQuery = clone $query;

        if ($user->role === 'porteiro') {
            $privateQuery->where('guard_id', $user->id);

            $this->submissions = $privateQuery->where('type', 'private')
                ->get()
                ->keyBy(fn($item) => Carbon::parse($item->start_date)->format('Y-m'));
        } else {
            $this->porteiros = User::where('role', 'porteiro')->orderBy('name')->get();

            $this->submissions = $privateQuery->where('type', 'private')
                ->get()
                ->groupBy('guard_id')
                ->map(fn($group) => $group->keyBy(fn($item) => Carbon::parse($item->start_date)->format('Y-m')));
        }
    }

    public function render()
    {
        return view('livewire.report-status');
    }
}
