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

    private function isSubmissionMonthValid(): bool
    {
        try {
            $selectedMonthStart = Carbon::parse($this->reportMonth . '-01')->startOfMonth();
            $currentMonthStart = Carbon::now()->startOfMonth();

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
        if (in_array($property, ['reportMonth', 'submissionType'])) {
            $this->resetPage('privatePage');
            $this->resetPage('officialPage');
            $this->reset('selectedVehicleId', 'selectedVehicleEntries', 'totalDistance', 'observation');
        }
    }

    public function setSubmissionType(string $type)
    {
        $this->submissionType = $type;
        $this->updated('submissionType');
    }

    public function selectVehicle(int $vehicleId)
    {
        $this->selectedVehicleId = $vehicleId;
        $startDate = Carbon::parse($this->reportMonth)->startOfMonth();
        $endDate = Carbon::parse($this->reportMonth)->endOfMonth();

        $this->selectedVehicleEntries = OfficialTrip::with(['driver', 'vehicle' => fn($q) => $q->withTrashed()])
            ->where('guard_on_arrival_id', Auth::id())
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
        $this->reset('selectedVehicleId', 'selectedVehicleEntries', 'observation', 'totalDistance');
    }

    private function getReportDates(): array
    {
        $date = Carbon::parse($this->reportMonth . '-01');
        return [
            'start' => $date->copy()->startOfMonth(),
            'end' => $date->copy()->endOfMonth(),
        ];
    }

    public function submitPrivateReport()
    {
        if (!$this->isSubmissionMonthValid()) return;
        $dates = $this->getReportDates();

        $existing = ReportSubmission::where('type', 'private')
            ->where('guard_id', Auth::id())
            ->whereYear('start_date', $dates['start']->year)
            ->whereMonth('start_date', $dates['start']->month)
            ->exists();

        if ($existing) {
            session()->flash('error', 'O relatório de particulares para este mês já foi submetido.');
            return;
        }

        $entryIds = PrivateEntry::query()
            ->where('guard_on_exit_id', Auth::id())
            ->whereNotNull('exit_at')
            ->whereBetween('entry_at', [$dates['start'], $dates['end']])
            ->whereNull('report_submission_id')
            ->pluck('id');

        if ($entryIds->isEmpty()) {
            session()->flash('error', 'Nenhum registro finalizado para submeter.');
            return;
        }

        $fiscal = User::where('role', 'fiscal')->whereIn('fiscal_type', ['private', 'both'])->inRandomOrder()->first();
        $submission = ReportSubmission::create([
            'guard_id' => Auth::id(),
            'assigned_fiscal_id' => $fiscal->id ?? null,
            'start_date' => $dates['start'],
            'end_date' => $dates['end'],
            'type' => 'private',
            'status' => 'pending',
            'submitted_at' => now(),
        ]);
        PrivateEntry::whereIn('id', $entryIds)->update(['report_submission_id' => $submission->id]);
        session()->flash('success', 'Relatório submetido com sucesso para visto da fiscalização!');

        $this->resetPage('privatePage');
    }

    public function submitOfficialReport()
    {
        if (!$this->isSubmissionMonthValid()) {
            $this->clearSelectedVehicle();
            return;
        }

        $this->validate([
            'selectedVehicleId' => 'required',
            'observation' => 'nullable|string|max:100',
            'selectedVehicleEntries' => 'required|array|min:1'
        ], ['selectedVehicleEntries.min' => 'Não há viagens finalizadas para reportar.']);

        $dates = $this->getReportDates();

        $existing = ReportSubmission::where('type', 'official')
            ->where('guard_id', Auth::id())
            ->where('vehicle_id', $this->selectedVehicleId)
            ->whereYear('start_date', $dates['start']->year)
            ->whereMonth('start_date', $dates['start']->month)
            ->exists();

        if ($existing) {
            session()->flash('error', 'Este relatório já foi submetido.');
            $this->clearSelectedVehicle();
            return;
        }

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
            'submitted_at' => now(),
        ]);
        OfficialTrip::whereIn('id', $this->selectedVehicleEntries->pluck('id'))->update(['report_submission_id' => $submission->id]);
        session()->flash('success', 'Relatório do veículo submetido com sucesso para visto!');

        $this->clearSelectedVehicle();
    }

    public function confirmSubmission(string $type)
    {
        if (!$this->isSubmissionMonthValid()) {
            if ($type === 'official') $this->clearSelectedVehicle();
            return;
        }
        $dates = $this->getReportDates();

        if ($type === 'private') {
            $count = PrivateEntry::query()
                ->where('guard_on_exit_id', Auth::id())
                ->whereNotNull('exit_at')
                ->whereBetween('entry_at', [$dates['start'], $dates['end']])
                ->whereNull('report_submission_id')
                ->count();

            if ($count === 0) {
                session()->flash('error', 'Nenhum registro finalizado para submeter.');
                return;
            }
            $this->confirmAction('submitPrivateReport', 'Confirmar Submissão', "Tem certeza que deseja enviar os {$count} registros de veículos particulares para " . $dates['start']->translatedFormat('F/Y') . "?");
        } elseif ($type === 'official') {
            $this->validate(['selectedVehicleId' => 'required']);
            $this->confirmAction('submitOfficialReport', 'Confirmar Submissão', 'Tem certeza que deseja enviar o relatório do veículo selecionado referente a ' . $dates['start']->translatedFormat('F/Y') . '?');
        }
    }

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

    public function render()
    {
        $dates = $this->getReportDates();
        $startDate = $dates['start'];
        $endDate = $dates['end'];

        $privateEntries = collect();
        $vehiclesWithOfficialTrips = collect();
        $officialTripsPaginator = null;

        if ($this->submissionType === 'private') {
            $privateEntries = PrivateEntry::with('vehicle', 'driver')
                ->where('guard_on_exit_id', Auth::id())
                ->whereNotNull('exit_at')
                ->whereBetween('entry_at', [$startDate, $endDate])
                ->whereNull('report_submission_id')
                ->orderBy('entry_at', 'desc')
                ->paginate(15, ['*'], 'privatePage');
        }

        if ($this->submissionType === 'official') {
            $baseQuery = OfficialTrip::query()
                ->where('guard_on_arrival_id', Auth::id())
                ->whereNotNull('arrival_datetime')
                ->whereBetween('departure_datetime', [$startDate, $endDate])
                ->whereNull('report_submission_id')
                ->whereHas('vehicle', fn($q) => $q->withTrashed());

            $allMatchingVehicleIds = $baseQuery->clone()->select('vehicle_id')->distinct()->pluck('vehicle_id');

            $perPage = 10;
            $currentPage = $this->getPage('officialPage');
            $pagedVehicleIds = $allMatchingVehicleIds->slice(($currentPage - 1) * $perPage, $perPage);

            $officialTripsPaginator = new LengthAwarePaginator(
                $pagedVehicleIds,
                $allMatchingVehicleIds->count(),
                $perPage,
                $currentPage,
                ['path' => request()->url(), 'pageName' => 'officialPage']
            );

            $tripsForCurrentPage = collect();
            if ($pagedVehicleIds->isNotEmpty()) {
                $tripsForCurrentPage = OfficialTrip::with(['vehicle' => fn($q) => $q->withTrashed()])
                    ->whereIn('vehicle_id', $pagedVehicleIds)
                    ->where('guard_on_arrival_id', Auth::id())
                    ->whereNotNull('arrival_datetime')
                    ->whereBetween('departure_datetime', [$startDate, $endDate])
                    ->whereNull('report_submission_id')
                    ->get();
            }

            $vehiclesWithOfficialTrips = $tripsForCurrentPage->groupBy('vehicle_id')->map(function ($vehicleTrips) {
                return [
                    'vehicle' => $vehicleTrips->first()->vehicle,
                    'count' => $vehicleTrips->count(),
                    'oldest_trip_date' => $vehicleTrips->min('departure_datetime'),
                ];
            });
        }

        return view('livewire.guard-report', [
            'privateEntries' => $privateEntries,
            'vehiclesWithOfficialTrips' => $vehiclesWithOfficialTrips,
            'officialTrips' => $officialTripsPaginator
        ]);
    }
}
