<?php

namespace App\Livewire;

use App\Models\Driver;
use App\Models\OfficialTrip;
use App\Models\PrivateEntry;
use App\Models\Vehicle;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class Reports extends Component
{
    use WithPagination;

    public string $reportType = 'official';
    public string $periodMode = 'day';
    public string $selectedDate;
    public string $selectedMonth;

    public $driver_id = null;
    public $vehicle_id = null;

    public string $driver_search = '';
    public string $driver_selected_text = '';
    public array $driver_results = [];

    public string $vehicle_search = '';
    public string $vehicle_selected_text = '';
    public array $vehicle_results = [];

    public function layoutData(): array
    {
        return ['header' => 'Consulta de Movimentações'];
    }

    public function mount(): void
    {
        $this->selectedDate = Carbon::today()->format('Y-m-d');
        $this->selectedMonth = Carbon::today()->format('Y-m');

        $user = Auth::user();
        if ($user->role === 'fiscal' && $user->fiscal_type === 'private') {
            $this->reportType = 'private';
        }
    }

    protected function rules(): array
    {
        return [
            'selectedDate' => [
                'required',
                'date_format:Y-m-d',
                function ($attribute, $value, $fail) {
                    try {
                        if (Carbon::parse($value)->startOfDay()->isAfter(Carbon::today())) {
                            $fail('Não é possível selecionar uma data futura.');
                        }
                    } catch (\Exception $e) {
                        $fail('Formato de data inválido.');
                    }
                },
            ],
            'selectedMonth' => [
                'required',
                'date_format:Y-m',
                function ($attribute, $value, $fail) {
                    try {
                        if (Carbon::parse($value . '-01')->startOfMonth()->isAfter(Carbon::today()->startOfMonth())) {
                            $fail('Não é possível selecionar um mês futuro.');
                        }
                    } catch (\Exception $e) {
                        $fail('Formato de mês inválido.');
                    }
                },
            ],
        ];
    }

    public function updatedReportType(): void
    {
        $this->driver_id = null;
        $this->vehicle_id = null;
        $this->driver_search = '';
        $this->vehicle_search = '';
        $this->driver_selected_text = '';
        $this->vehicle_selected_text = '';
        $this->driver_results = [];
        $this->vehicle_results = [];
        $this->resetPage();
    }

    public function updated($property): void
    {
        if ($property === 'selectedDate' && $this->periodMode === 'day') {
            $this->validateOnly('selectedDate');
            $this->resetPage();
        }

        if ($property === 'selectedMonth' && $this->periodMode === 'month') {
            $this->validateOnly('selectedMonth');
            $this->resetPage();
        }

        if (in_array($property, ['driver_id', 'vehicle_id'], true)) {
            $this->resetPage();
        }
    }

    public function setPeriodMode(string $mode): void
    {
        if (!in_array($mode, ['day', 'month'], true)) {
            return;
        }

        $this->periodMode = $mode;

        if ($mode === 'day' && empty($this->selectedDate)) {
            $this->selectedDate = Carbon::today()->format('Y-m-d');
        } elseif ($mode === 'month' && empty($this->selectedMonth)) {
            $this->selectedMonth = Carbon::today()->format('Y-m');
        }

        $this->resetValidation(['selectedDate', 'selectedMonth']);
        $this->resetPage();
    }

    public function setToday(): void
    {
        $this->periodMode = 'day';
        $this->selectedDate = Carbon::today()->format('Y-m-d');
        $this->resetValidation(['selectedDate']);
        $this->resetPage();
    }

    public function getOfficialVehiclesProperty()
    {
        return Vehicle::withTrashed()
            ->where('type', 'Oficial')
            ->orderBy('model')
            ->get();
    }

    public function getOfficialDriversProperty()
    {
        return Driver::withTrashed()
            ->where('is_authorized', true)
            ->orderBy('name')
            ->get();
    }

    public function runSearch($model, $value): void
    {
        $value = trim($value);

        if (strlen($value) < 2) {
            if ($model === 'driver_search') {
                $this->driver_results = [];
            }
            if ($model === 'vehicle_search') {
                $this->vehicle_results = [];
            }
            return;
        }

        if ($model === 'driver_search') {
            $this->driver_results = Driver::withTrashed()
                ->where('name', 'like', "%{$value}%")
                ->limit(10)
                ->get()
                ->toArray();
        }

        if ($model === 'vehicle_search') {
            $this->vehicle_results = Vehicle::withTrashed()
                ->where('type', 'Particular')
                ->where(function ($q) use ($value) {
                    $q->where('model', 'like', "%{$value}%")
                        ->orWhere('license_plate', 'like', "%{$value}%");
                })
                ->limit(10)
                ->get()
                ->toArray();
        }
    }

    public function selectResult($model, $id, $text): void
    {
        if ($model === 'driver_search') {
            $this->driver_id = $id;
            $this->driver_selected_text = $text;
            $this->driver_results = [];
            $this->driver_search = '';
        } elseif ($model === 'vehicle_search') {
            $this->vehicle_id = $id;
            $this->vehicle_selected_text = $text;
            $this->vehicle_results = [];
            $this->vehicle_search = '';
        }

        $this->resetPage();
    }

    public function clearSelection($model): void
    {
        if ($model === 'driver_search') {
            $this->driver_id = null;
            $this->driver_selected_text = '';
            $this->driver_results = [];
            $this->driver_search = '';
        } elseif ($model === 'vehicle_search') {
            $this->vehicle_id = null;
            $this->vehicle_selected_text = '';
            $this->vehicle_results = [];
            $this->vehicle_search = '';
        }

        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->periodMode = 'day';
        $this->selectedDate = Carbon::today()->format('Y-m-d');
        $this->selectedMonth = Carbon::today()->format('Y-m');
        $this->driver_id = null;
        $this->vehicle_id = null;
        $this->driver_search = '';
        $this->vehicle_search = '';
        $this->driver_selected_text = '';
        $this->vehicle_selected_text = '';
        $this->driver_results = [];
        $this->vehicle_results = [];
        $this->resetValidation();
        $this->resetPage();
    }

    private function resolvePeriod(): array
    {
        try {
            if ($this->periodMode === 'month') {
                $this->validateOnly('selectedMonth');
                $month = Carbon::parse($this->selectedMonth . '-01');

                return [
                    $month->copy()->startOfMonth(),
                    $month->copy()->endOfMonth(),
                    $month->translatedFormat('F/Y'),
                ];
            }

            $this->validateOnly('selectedDate');
            $date = Carbon::parse($this->selectedDate);

            return [
                $date->copy()->startOfDay(),
                $date->copy()->endOfDay(),
                $date->isToday() ? 'Hoje - ' . $date->format('d/m/Y') : $date->format('d/m/Y'),
            ];
        } catch (\Exception $e) {
            $today = Carbon::today();
            $this->periodMode = 'day';
            $this->selectedDate = $today->format('Y-m-d');

            return [
                $today->copy()->startOfDay(),
                $today->copy()->endOfDay(),
                'Hoje - ' . $today->format('d/m/Y'),
            ];
        }
    }

    public function render()
    {
        $user = Auth::user();

        $canViewPrivate = true;
        $canViewOfficial = true;

        if ($user->role === 'fiscal') {
            if ($user->fiscal_type === 'private') {
                $canViewOfficial = false;
                $this->reportType = 'private';
            }
            if ($user->fiscal_type === 'official') {
                $canViewPrivate = false;
                $this->reportType = 'official';
            }
        }

        [$startDate, $endDate, $periodLabel] = $this->resolvePeriod();

        if ($this->reportType === 'official') {
            $query = OfficialTrip::with([
                'vehicle' => fn ($q) => $q->withTrashed(),
                'driver' => fn ($q) => $q->withTrashed(),
                'guardDeparture',
                'guardArrival',
            ])->whereBetween('departure_datetime', [$startDate, $endDate]);

            if ($this->vehicle_id) {
                $query->where('vehicle_id', $this->vehicle_id);
            }
            if ($this->driver_id) {
                $query->where('driver_id', $this->driver_id);
            }

            $total = (clone $query)->count();
            $open = (clone $query)->whereNull('arrival_datetime')->count();
            $completed = $total - $open;

            $results = $query
                ->orderBy('departure_datetime', 'desc')
                ->paginate(15);
        } else {
            $query = PrivateEntry::with([
                'vehicle' => fn ($q) => $q->withTrashed(),
                'driver' => fn ($q) => $q->withTrashed(),
                'guardEntry',
                'guardExit',
            ])->whereBetween('entry_at', [$startDate, $endDate]);

            if ($this->vehicle_id) {
                $query->where('vehicle_id', $this->vehicle_id);
            }
            if ($this->driver_id) {
                $query->where('driver_id', $this->driver_id);
            }

            $total = (clone $query)->count();
            $open = (clone $query)->whereNull('exit_at')->count();
            $completed = $total - $open;

            $results = $query
                ->orderBy('entry_at', 'desc')
                ->paginate(15);
        }

        return view('livewire.reports', [
            'results' => $results,
            'pdfStartDate' => $startDate->format('Y-m-d'),
            'pdfEndDate' => $endDate->format('Y-m-d'),
            'periodLabel' => $periodLabel,
            'summary' => [
                'total' => $total,
                'completed' => $completed,
                'open' => $open,
            ],
            'canViewPrivate' => $canViewPrivate,
            'canViewOfficial' => $canViewOfficial,
        ]);
    }
}
