<?php

namespace App\Livewire;


use Livewire\Component;
use App\Models\OfficialTrip;
use App\Models\PrivateEntry;
use App\Models\Vehicle;
use App\Models\Driver;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth; // Adicionado, caso precise no futuro
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use Illuminate\Support\Str; 

#[Layout('layouts.app')]
class Reports extends Component
{
    use WithPagination;

    // --- Propriedades para os Filtros ---
    public string $reportType = 'official'; // 'official' ou 'private' (valor ajustado)
    // ### ALTERAÇÃO: Troca startDate/endDate por selectedMonth ###
    // public $startDate;
    // public $endDate;
    public string $selectedMonth;

    // Mantidos como no seu original
    public $selectedVehicle = '';
    public $selectedDriver = '';

    // --- Propriedades do Dashboard REMOVIDAS ---

    /**
     * Define o cabeçalho da página.
     */
    public function layoutData()
    {
        // Simplificado
        return ['header' => 'Relatórios Gerenciais'];
    }

    /**
     * Inicializa o componente.
     */
    public function mount()
    {
        // ### ALTERAÇÃO: Define o MÊS ANTERIOR como padrão ###
        $this->selectedMonth = Carbon::now()->subMonthNoOverflow()->format('Y-m');
        // Dashboard removido
    }

    /**
     * Removemos generateReport, a filtragem é reativa.
     * Adicionamos validação para o mês.
     */
    protected function rules()
    {
        return [
            'selectedMonth' => [
                'required',
                'date_format:Y-m',
                function ($attribute, $value, $fail) {
                    try {
                        // Impede seleção de mês futuro
                        if (Carbon::parse($value . '-01')->startOfMonth()->isFuture()) {
                            $fail('Não é possível selecionar um mês futuro.');
                        }
                    } catch (\Exception $e) {
                        $fail('Formato de mês inválido.');
                    }
                }
            ],
            // Validação básica para filtros opcionais
            'selectedVehicle' => 'nullable|exists:vehicles,id',
            'selectedDriver' => 'nullable|exists:drivers,id',
        ];
    }


    /**
     * Reseta filtros específicos ao mudar o tipo de relatório.
     */
    public function updatedReportType()
    {
        $this->reset('selectedVehicle', 'selectedDriver');
        $this->resetPage();
    }

    /**
     * Reseta a paginação ao mudar filtros e valida o mês.
     */
    public function updated($property)
    {
        if (in_array($property, ['selectedMonth', 'selectedVehicle', 'selectedDriver'])) {
            // Valida apenas o campo alterado
            $this->validateOnly($property);
            $this->resetPage();
        }
    }

    /**
     * Propriedade computada para veículos (mantida como no seu original).
     * Retorna array [id => description] para o select.
     */
    public function getVehiclesProperty()
    {
        $query = Vehicle::query()->select('id', 'model', 'license_plate')->withTrashed(); // Inclui lixeira

        if ($this->reportType === 'official') { // Usa 'official'
            $query->where('type', 'Oficial');
        }

        // Usa mapWithKeys como no seu original
        return $query->orderBy('model')->get()->mapWithKeys(function ($vehicle) {
            return [$vehicle->id => "[{$vehicle->license_plate}] " . Str::limit($vehicle->model, 30)]; // Limita o nome
        });
    }

    /**
     * Propriedade computada para motoristas (mantida como no seu original).
     * Retorna array [id => name] para o select.
     */
    public function getDriversProperty()
    {
        $query = Driver::query()->select('id', 'name')->withTrashed(); // Inclui lixeira

        if ($this->reportType === 'official') { // Usa 'official'
            $query->where('is_authorized', true);
        }

        // Usa pluck como no seu original
        return $query->orderBy('name')->pluck('name', 'id');
    }


    public function render()
    {
        // --- LÓGICA DO DASHBOARD REMOVIDA ---

        // --- LÓGICA DA TABELA DE RELATÓRIOS DETALHADOS ---
        $results = null;
        $startDate = null;
        $endDate = null;
        $pdfStartDate = null; // Data formatada para link PDF
        $pdfEndDate = null;   // Data formatada para link PDF

        // Tenta obter as datas do mês selecionado
        try {
            // Garante que usa o mês validado mais recente
            $validatedMonth = $this->validateOnly('selectedMonth')['selectedMonth'] ?? $this->selectedMonth;
            $month = Carbon::parse($validatedMonth . '-01');
            $startDate = $month->copy()->startOfMonth();
            $endDate = $month->copy()->endOfMonth();
            $pdfStartDate = $startDate->format('Y-m-d'); // Formato para URL
            $pdfEndDate = $endDate->format('Y-m-d');     // Formato para URL

        } catch (\Exception $e) {
            // Fallback em caso de erro no parse do mês
            $fallbackDate = Carbon::now()->subMonthNoOverflow();
            $startDate = $fallbackDate->copy()->startOfMonth();
            $endDate = $fallbackDate->copy()->endOfMonth();
            $pdfStartDate = $startDate->format('Y-m-d');
            $pdfEndDate = $endDate->format('Y-m-d');
            // Atualiza a propriedade pública para refletir o fallback na UI
            $this->selectedMonth = $startDate->format('Y-m');
            if (!session()->has('error')) {
                session()->flash('error', 'Mês inválido, mostrando dados do mês anterior.');
            }
        }


        // Constrói a query baseada no tipo de relatório
        if ($startDate && $endDate) { // Só executa a query se as datas forem válidas
            if ($this->reportType === 'official') {
                $query = OfficialTrip::with(['vehicle' => fn($q) => $q->withTrashed(), 'driver' => fn($q) => $q->withTrashed()])
                    ->whereNotNull('arrival_datetime')
                    ->whereBetween('departure_datetime', [$startDate, $endDate]);
            } else { // private
                $query = PrivateEntry::with(['vehicle' => fn($q) => $q->withTrashed(), 'driver' => fn($q) => $q->withTrashed()])
                    ->whereNotNull('exit_at')
                    ->whereBetween('entry_at', [$startDate, $endDate]);
            }

            // Aplica filtros opcionais (usando nomes originais das propriedades)
            if ($this->selectedVehicle) {
                $query->where('vehicle_id', $this->selectedVehicle);
            }
            if ($this->selectedDriver) {
                $query->where('driver_id', $this->selectedDriver);
            }

            // Ordena e pagina
            $results = $query->orderBy($this->reportType === 'official' ? 'departure_datetime' : 'entry_at', 'desc')
                ->paginate(15);
        } else {
            // Se as datas não puderam ser calculadas, retorna coleção vazia paginada
            $results = collect()->paginate(15);
        }


        return view('livewire.reports', [
            'results' => $results,
            'pdfStartDate' => $pdfStartDate, // Passa data formatada para link
            'pdfEndDate' => $pdfEndDate,     // Passa data formatada para link
            // As propriedades selectedVehicle e selectedDriver já são públicas
        ])->layoutData(['header' => 'Relatórios Gerenciais']);
    }
}
