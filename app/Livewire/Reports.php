<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\OfficialTrip;
use App\Models\PrivateEntry;
use App\Models\Vehicle;
use App\Models\Driver;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use Illuminate\Support\Str;

#[Layout('layouts.app')]
class Reports extends Component
{
    use WithPagination;

    public string $reportType = 'official';
    public string $selectedMonth;

    public $driver_id = null;
    public $vehicle_id = null;

    public string $driver_search = '';
    public string $driver_selected_text = '';
    public array $driver_results = [];

    public string $vehicle_search = '';
    public string $vehicle_selected_text = '';
    public array $vehicle_results = [];

    public function layoutData()
    {
        return ['header' => 'Pesquisa e Extratos Avançados'];
    }

    public function mount()
    {
        $this->selectedMonth = Carbon::now()->subMonthNoOverflow()->format('Y-m');

        $user = Auth::user();
        if ($user->role === 'fiscal' && $user->fiscal_type === 'private') {
            $this->reportType = 'private';
        }
    }

    protected function rules()
    {
        return [
            'selectedMonth' => [
                'required',
                'date_format:Y-m',
                function ($attribute, $value, $fail) {
                    try {
                        if (Carbon::parse($value . '-01')->startOfMonth()->isFuture()) {
                            $fail('Não é possível selecionar um mês futuro.');
                        }
                    } catch (\Exception $e) {
                        $fail('Formato de mês inválido.');
                    }
                }
            ]
        ];
    }

    public function updatedReportType()
    {
        $this->clearFilters();
    }

    public function updated($property)
    {
        if (in_array($property, ['selectedMonth', 'driver_id', 'vehicle_id'])) {
            if ($property === 'selectedMonth') $this->validateOnly($property);
            $this->resetPage();
        }
    }

    public function getOfficialVehiclesProperty()
    {
        return Vehicle::withTrashed()->where('type', 'Oficial')->orderBy('model')->get();
    }

    public function getOfficialDriversProperty()
    {
        return Driver::withTrashed()->where('is_authorized', true)->orderBy('name')->get();
    }

    public function runSearch($model, $value)
    {
        $value = trim($value);

        if (strlen($value) < 2) {
            if ($model === 'driver_search') $this->driver_results = [];
            if ($model === 'vehicle_search') $this->vehicle_results = [];
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

    public function selectResult($model, $id, $text)
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

    public function clearSelection($model)
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

    public function clearFilters()
    {
        $this->selectedMonth = Carbon::now()->subMonthNoOverflow()->format('Y-m');
        $this->driver_id = null;
        $this->vehicle_id = null;
        $this->driver_selected_text = '';
        $this->vehicle_selected_text = '';
        $this->resetPage();
    }

    public function render()
    {
        $user = Auth::user();
        $isPorteiro = $user->role === 'porteiro';

        $canViewPrivate = true;
        $canViewOfficial = true;

        if ($user->role === 'fiscal') {
            if ($user->fiscal_type === 'private') $canViewOfficial = false;
            if ($user->fiscal_type === 'official') $canViewPrivate = false;
        }

        $results = null;
        $startDate = null;
        $endDate = null;
        $pdfStartDate = null;
        $pdfEndDate = null;

        try {
            $validatedMonth = $this->validateOnly('selectedMonth')['selectedMonth'] ?? $this->selectedMonth;
            $month = Carbon::parse($validatedMonth . '-01');
            $startDate = $month->copy()->startOfMonth();
            $endDate = $month->copy()->endOfMonth();
            $pdfStartDate = $startDate->format('Y-m-d');
            $pdfEndDate = $endDate->format('Y-m-d');
        } catch (\Exception $e) {
            $fallbackDate = Carbon::now()->subMonthNoOverflow();
            $startDate = $fallbackDate->copy()->startOfMonth();
            $endDate = $fallbackDate->copy()->endOfMonth();
            $pdfStartDate = $startDate->format('Y-m-d');
            $pdfEndDate = $endDate->format('Y-m-d');
            $this->selectedMonth = $startDate->format('Y-m');
        }

        if ($startDate && $endDate) {

            // CONSERTEI AQUI: A divisão limpa entre as Abas
            if ($this->reportType === 'official') {
                $query = OfficialTrip::with(['vehicle' => fn($q) => $q->withTrashed(), 'driver' => fn($q) => $q->withTrashed(), 'guardDeparture', 'guardArrival'])
                    ->whereNotNull('arrival_datetime')
                    ->whereBetween('departure_datetime', [$startDate, $endDate]);

                // BARREIRA INVISÍVEL PARA O PORTEIRO (CHEGADAS)
                if ($isPorteiro) {
                    $query->where('guard_on_arrival_id', $user->id);
                }
            } else {
                $query = PrivateEntry::with(['vehicle' => fn($q) => $q->withTrashed(), 'driver' => fn($q) => $q->withTrashed(), 'guardEntry', 'guardExit'])
                    ->whereNotNull('exit_at')
                    ->whereBetween('entry_at', [$startDate, $endDate]);

                // BARREIRA INVISÍVEL PARA O PORTEIRO (SAÍDAS)
                if ($isPorteiro) {
                    $query->where('guard_on_exit_id', $user->id);
                }
            }

            if ($this->vehicle_id) {
                $query->where('vehicle_id', $this->vehicle_id);
            }
            if ($this->driver_id) {
                $query->where('driver_id', $this->driver_id);
            }

            $results = $query->orderBy($this->reportType === 'official' ? 'departure_datetime' : 'entry_at', 'desc')
                ->paginate(15);
        } else {
            $results = collect()->paginate(15);
        }

        return view('livewire.reports', [
            'results' => $results,
            'pdfStartDate' => $pdfStartDate,
            'pdfEndDate' => $pdfEndDate,
            'canViewPrivate' => $canViewPrivate,
            'canViewOfficial' => $canViewOfficial,
            'isPorteiro' => $isPorteiro
        ]);
    }
}
