<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\PrivateEntry;
use App\Models\OfficialTrip;
use App\Models\ReportSubmission;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class GuardReport extends Component
{
    use WithPagination;

    // Propriedades (mantidas)
    public string $reportMonth;
    public string $submissionType = 'private';
    public string $observation = '';
    public ?int $selectedVehicleId = null;
    public Collection $selectedVehicleEntries;
    public int $totalDistance = 0;
    public bool $showConfirmationModal = false;
    public string $confirmationTitle = '';
    public string $confirmationMessage = '';
    public string $confirmedAction = '';
    public array $confirmedParams = [];

    public function mount()
    {
        $this->reportMonth = Carbon::now()->subMonth()->format('Y-m');
        $this->selectedVehicleEntries = collect();
    }
    /**
     * Verifica se o mês selecionado é válido para submissão.
     * Retorna true se for válido, false caso contrário.
     */
    private function isSubmissionMonthValid(): bool
    {
        try {
            $selectedMonthStart = Carbon::parse($this->reportMonth . '-01')->startOfMonth();
            $currentMonthStart = Carbon::now()->startOfMonth();

            // Permite submeter apenas se o início do mês selecionado for ANTERIOR ao início do mês atual
            if ($selectedMonthStart->lt($currentMonthStart)) {
                return true;
            } else {
                session()->flash('error', 'Só é permitido submeter relatórios de meses anteriores ao mês atual.');
                return false;
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Mês selecionado inválido.');
            return false;
        }
    }
    public function updated($property)
    {
        // (Lógica mantida)
        if (in_array($property, ['reportMonth', 'submissionType'])) {
            $this->resetPage('privatePage');
            $this->resetPage('officialPage');
            $this->reset('selectedVehicleId', 'selectedVehicleEntries', 'totalDistance', 'observation');
        }
    }

    public function setSubmissionType(string $type)
    {
        // (Lógica mantida)
        $this->submissionType = $type;
        $this->updated('submissionType');
    }

    /**
     * Busca as viagens oficiais finalizadas pelo porteiro logado
     * para o veículo e mês selecionados.
     */
    public function selectVehicle(int $vehicleId)
    {
        $this->selectedVehicleId = $vehicleId;
        $startDate = Carbon::parse($this->reportMonth)->startOfMonth();
        $endDate = Carbon::parse($this->reportMonth)->endOfMonth();

        $this->selectedVehicleEntries = OfficialTrip::with(['driver', 'vehicle' => fn($q) => $q->withTrashed()])
            // ### CORREÇÃO 1 (Oficiais - Feita ✅) ###
            // A responsabilidade é de quem registou a CHEGADA.
            ->where('guard_on_arrival_id', Auth::id()) // <-- Correto! Usa ID.
            // Garante que a viagem está finalizada para poder ser reportada.
            ->whereNotNull('arrival_datetime')
            ->where('vehicle_id', $this->selectedVehicleId)
            ->whereBetween('departure_datetime', [$startDate, $endDate])
            ->whereNull('report_submission_id')
            ->orderBy('departure_datetime', 'asc')
            ->get();

        $this->totalDistance = $this->selectedVehicleEntries->sum('distance_traveled');
    }

    public function clearSelectedVehicle()
    {
        // (Lógica mantida)
        $this->reset('selectedVehicleId', 'selectedVehicleEntries', 'observation', 'totalDistance');
    }

    private function getReportDates(): array
    {
        // (Lógica mantida)
        $date = Carbon::parse($this->reportMonth . '-01');
        return [
            'start' => $date->copy()->startOfMonth(),
            'end' => $date->copy()->endOfMonth(),
        ];
    }

    /**
     * Submete o relatório de veículos particulares para o mês selecionado.
     */
    public function submitPrivateReport()
    {
        if (!$this->isSubmissionMonthValid()) {
            return; // Interrompe se o mês não for válido
        }
        $dates = $this->getReportDates();

        // Verificação de existência (Já estava correta, usando guard_id)
        $existing = ReportSubmission::where('type', 'private')
            ->where('guard_id', Auth::id())
            ->whereYear('start_date', $dates['start']->year)
            ->whereMonth('start_date', $dates['start']->month)
            ->exists();

        if ($existing) {
            session()->flash('error', 'Um relatório de veículos particulares para este mês já foi submetido.');
            return;
        }

        // Busca os IDs das entradas a serem incluídas no relatório
        $entryIds = PrivateEntry::query()
            // ### CORREÇÃO 2 (Particulares - Feita ✅) ###
            // A responsabilidade é de quem registou a SAÍDA.
            ->where('guard_on_exit_id', Auth::id()) // <-- Correto! Usa ID.
            // Garante que o ciclo está finalizado.
            ->whereNotNull('exit_at')
            ->whereBetween('entry_at', [$dates['start'], $dates['end']])
            ->whereNull('report_submission_id')
            ->pluck('id');

        if ($entryIds->isEmpty()) {
            session()->flash('error', 'Nenhum registro de veículo particular finalizado para submeter no mês.');
            return;
        }

        // (Lógica de criação da submissão mantida - já usa Auth::id() corretamente)
        $fiscal = User::where('role', 'fiscal')->whereIn('fiscal_type', ['private', 'both'])->inRandomOrder()->first();

        $submission = ReportSubmission::create([
            'guard_id' => Auth::id(),
            'assigned_fiscal_id' => $fiscal->id ?? null,
            'start_date' => $dates['start'],
            'end_date' => $dates['end'],
            'type' => 'private',
            'status' => 'pending',
        ]);

        // Associa as entradas à submissão
        PrivateEntry::whereIn('id', $entryIds)->update(['report_submission_id' => $submission->id]);
        session()->flash('success', 'Relatório de ' . $entryIds->count() . ' registros particulares submetido com sucesso!');
        $this->resetPage('privatePage');
    }

    /**
     * Submete o relatório do veículo oficial selecionado para o mês.
     */
    public function submitOfficialReport()
    {
        if (!$this->isSubmissionMonthValid()) {
            // Limpa o veículo selecionado se a submissão for inválida
            // para evitar confusão na UI caso o usuário tente de novo
            $this->clearSelectedVehicle();
            return; // Interrompe se o mês não for válido
        }
        // (Validação mantida)
        $this->validate([
            'selectedVehicleId' => 'required',
            'observation' => 'nullable|string|max:100',
            'selectedVehicleEntries' => 'required|array|min:1'
        ], ['selectedVehicleEntries.min' => 'Não há viagens finalizadas para reportar para este veículo no período.']);

        $dates = $this->getReportDates();

        // Verificação de existência
        // ### CORREÇÃO DO BUG ANTERIOR (Feita ✅) ###
        $existing = ReportSubmission::where('type', 'official')
            ->where('guard_id', Auth::id()) // <-- Correto! Adicionado guard_id.
            ->where('vehicle_id', $this->selectedVehicleId)
            ->whereYear('start_date', $dates['start']->year)
            ->whereMonth('start_date', $dates['start']->month)
            ->exists();

        if ($existing) {
            // Mensagem de erro também corrigida
            session()->flash('error', 'Você já submeteu um relatório para este veículo no mês selecionado.');
            $this->clearSelectedVehicle();
            return;
        }

        // (Lógica de criação da submissão mantida - já usa Auth::id() corretamente)
        $fiscal = User::where('role', 'fiscal')->whereIn('fiscal_type', ['official', 'both'])->inRandomOrder()->first();

        $submission = ReportSubmission::create([
            'guard_id' => Auth::id(),
            'assigned_fiscal_id' => $fiscal->id ?? null,
            'vehicle_id' => $this->selectedVehicleId,
            'start_date' => $dates['start'],
            'end_date' => $dates['end'],
            'observation' => $this->observation,
            'type' => 'official',
            'status' => 'pending',
        ]);

        // Associa as viagens à submissão
        OfficialTrip::whereIn('id', $this->selectedVehicleEntries->pluck('id'))->update(['report_submission_id' => $submission->id]);
        session()->flash('success', 'Relatório do veículo submetido com sucesso!');
        $this->clearSelectedVehicle();
    }

    /**
     * Prepara a confirmação antes de submeter o relatório.
     */
    public function confirmSubmission(string $type)
    {

        // Verifica antes mesmo de mostrar a confirmação
        if (!$this->isSubmissionMonthValid()) {
            // Limpa seleção se for oficial e inválido
            if ($type === 'official') $this->clearSelectedVehicle();
            return;
        }
        $dates = $this->getReportDates();

        if ($type === 'private') {
            // Conta as entradas particulares finalizadas pelo porteiro no mês
            $count = PrivateEntry::query()
                // ### CORREÇÃO 3 (Contagem Particulares - Feita ✅) ###
                ->where('guard_on_exit_id', Auth::id()) // <-- Correto! Usa ID.
                ->whereNotNull('exit_at')
                ->whereBetween('entry_at', [$dates['start'], $dates['end']])
                ->whereNull('report_submission_id')
                ->count();

            if ($count === 0) {
                session()->flash('error', 'Nenhum registro finalizado para submeter.');
                return;
            }
            $this->confirmAction('submitPrivateReport', 'Confirmar Submissão', "Tem certeza que deseja submeter os {$count} registros de veículos particulares para " . $dates['start']->translatedFormat('F/Y') . "?");
        } elseif ($type === 'official') {
            // (Validação mantida)
            $this->validate(['selectedVehicleId' => 'required']);
            // A contagem para oficiais é feita implicitamente pela validação de $selectedVehicleEntries em submitOfficialReport
            $this->confirmAction('submitOfficialReport', 'Confirmar Submissão', 'Tem certeza que deseja submeter o relatório para o veículo selecionado referente a ' . $dates['start']->translatedFormat('F/Y') . '?');
        }
    }

    // --- Métodos do Modal de Confirmação (mantidos) ---
    public function confirmAction(string $action, string $title, string $message, array $params = [])
    {
        $this->confirmedAction = $action;
        $this->confirmationTitle = $title;
        $this->confirmationMessage = $message;
        $this->confirmedParams = $params;
        $this->showConfirmationModal = true;
    }

    public function executeConfirmedAction()
    {
        if (method_exists($this, $this->confirmedAction)) {
            call_user_func_array([$this, $this->confirmedAction], $this->confirmedParams);
        }
        $this->showConfirmationModal = false;
    }
    // --- Fim dos Métodos do Modal ---


    /**
     * Renderiza o componente, buscando os dados necessários.
     */
    public function render()
    {
        $dates = $this->getReportDates();
        $startDate = $dates['start'];
        $endDate = $dates['end'];

        $privateEntries = collect();
        $vehiclesWithOfficialTrips = collect();
        $officialTripsPaginator = null;

        // Busca entradas particulares para a tabela
        if ($this->submissionType === 'private') {
            $privateEntries = PrivateEntry::with('vehicle', 'driver')
                // ### CORREÇÃO 4 (Listagem Particulares - Feita ✅) ###
                ->where('guard_on_exit_id', Auth::id()) // <-- Correto! Usa ID.
                ->whereNotNull('exit_at')
                ->whereBetween('entry_at', [$startDate, $endDate])
                ->whereNull('report_submission_id')
                ->orderBy('entry_at', 'desc')
                ->paginate(15, ['*'], 'privatePage');
        }

        // Busca viagens/veículos oficiais para a lista
        if ($this->submissionType === 'official') {
            // Query base para encontrar veículos com viagens finalizadas pelo porteiro no mês
            $baseQuery = OfficialTrip::query()
                // ### CORREÇÃO 5 (Listagem Oficiais - Base Query - Feita ✅) ###
                ->where('guard_on_arrival_id', Auth::id()) // <-- Correto! Usa ID.
                ->whereNotNull('arrival_datetime')
                ->whereBetween('departure_datetime', [$startDate, $endDate])
                ->whereNull('report_submission_id')
                ->whereHas('vehicle', fn($q) => $q->withTrashed()); // Garante que o veículo existe (mesmo deletado)

            // Pega todos os IDs de veículos que correspondem
            $allMatchingVehicleIds = $baseQuery->clone()->select('vehicle_id')->distinct()->pluck('vehicle_id');

            // Paginação manual dos IDs dos veículos
            $perPage = 10;
            $currentPage = $this->getPage('officialPage');
            $pagedVehicleIds = $allMatchingVehicleIds->slice(($currentPage - 1) * $perPage, $perPage);

            $officialTripsPaginator = new LengthAwarePaginator(
                $pagedVehicleIds, // Os itens da página atual são os IDs
                $allMatchingVehicleIds->count(), // Total de veículos
                $perPage,
                $currentPage,
                ['path' => request()->url(), 'pageName' => 'officialPage']
            );

            // Busca os detalhes das viagens APENAS para os veículos da página atual
            $tripsForCurrentPage = collect();
            if ($pagedVehicleIds->isNotEmpty()) {
                $tripsForCurrentPage = OfficialTrip::with(['vehicle' => fn($q) => $q->withTrashed()])
                    ->whereIn('vehicle_id', $pagedVehicleIds)
                    // ### CORREÇÃO 6 (Consistência na busca - Feita ✅) ###
                    // Repete as condições da query base para buscar os detalhes corretos
                    ->where('guard_on_arrival_id', Auth::id()) // <-- Correto! Usa ID.
                    ->whereNotNull('arrival_datetime')
                    ->whereBetween('departure_datetime', [$startDate, $endDate])
                    ->whereNull('report_submission_id')
                    ->get();
            }

            // Agrupa as viagens por veículo para exibir na lista
            $vehiclesWithOfficialTrips = $tripsForCurrentPage->groupBy('vehicle_id')->map(function ($vehicleTrips) {
                return [
                    'vehicle' => $vehicleTrips->first()->vehicle, // Pega os dados do veículo
                    'count' => $vehicleTrips->count(), // Conta quantas viagens por veículo
                    'oldest_trip_date' => $vehicleTrips->min('departure_datetime'), // Data da viagem mais antiga (apenas informativo)
                ];
            });
        }

        return view('livewire.guard-report', [
            'privateEntries' => $privateEntries,
            'vehiclesWithOfficialTrips' => $vehiclesWithOfficialTrips,
            'officialTrips' => $officialTripsPaginator, // Passa o paginador para a view
        ]);
    }
}
