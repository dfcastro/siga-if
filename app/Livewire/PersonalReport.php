<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\OfficialTrip;
use App\Models\PrivateEntry;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth; // Use Auth facade
use Livewire\Attributes\Layout;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class PersonalReport extends Component
{
    use WithPagination;

    public $reportType = 'particular'; // Pode ajustar para 'private' se for mais consistente
    public $startDate;
    public $endDate;

    public function mount()
    {
        $this->startDate = Carbon::now()->startOfMonth()->format('Y-m-d');
        $this->endDate = Carbon::now()->endOfMonth()->format('Y-m-d');
    }

    public function updating($property)
    {
        if (in_array($property, ['reportType', 'startDate', 'endDate'])) {
            $this->resetPage();
        }
    }

    public function render()
    {
        $porteiroId = Auth::id(); // <-- USA O ID
        $porteiroName = Auth::user()->name; // Mantém para exibição na view
        $results = null;

        // Valida as datas para evitar erros na query
        try {
            $start = Carbon::parse($this->startDate)->startOfDay();
            $end = Carbon::parse($this->endDate)->endOfDay();
        } catch (\Exception $e) {
            // Se as datas forem inválidas, define um período padrão ou retorna erro
            $start = Carbon::now()->startOfMonth()->startOfDay();
            $end = Carbon::now()->endOfMonth()->endOfDay();
            // Opcional: Adicionar mensagem de erro para o usuário
             session()->flash('error', 'Datas inválidas selecionadas. Mostrando o mês atual.');
             $this->startDate = $start->format('Y-m-d');
             $this->endDate = $end->format('Y-m-d');
        }


        if ($this->reportType === 'particular' || $this->reportType === 'private') { // Aceita ambos os nomes
            $query = PrivateEntry::with([
                    'vehicle' => fn($q) => $q->withTrashed(), // Carrega veículo (mesmo excluído)
                    'driver' => fn($q) => $q->withTrashed(),  // Carrega motorista (mesmo excluído)
                    'guardEntry',                            // Carrega porteiro da entrada
                    'guardExit'                              // Carrega porteiro da saída
                ])
                // ### CORREÇÃO AQUI ###
                // Filtra pelos registros onde o porteiro logado registrou a ENTRADA
                // ->where('guard_on_entry', $porteiroName) // <-- REMOVIDO
                ->where('guard_on_entry_id', $porteiroId)   // <-- CORRIGIDO para ID
                // Poderia adicionar ->orWhere('guard_on_exit_id', $porteiroId) se quisesse mostrar entradas OU saídas
                ->whereBetween('entry_at', [$start, $end]);

            $results = $query->orderBy('entry_at', 'desc')->paginate(15);

        } elseif ($this->reportType === 'oficial' || $this->reportType === 'official') { // Aceita ambos os nomes
            $query = OfficialTrip::with([
                    'vehicle' => fn($q) => $q->withTrashed(),
                    'driver' => fn($q) => $q->withTrashed(),
                    'guardDeparture',                        // Carrega porteiro da partida
                    'guardArrival'                           // Carrega porteiro da chegada
                ])
                
                // Filtra pelas viagens onde o porteiro logado registrou a PARTIDA
                // ->where('guard_on_departure', $porteiroName) // <-- REMOVIDO
                ->where('guard_on_departure_id', $porteiroId)   // <-- CORRIGIDO para ID
                // Poderia adicionar ->orWhere('guard_on_arrival_id', $porteiroId) se quisesse mostrar partidas OU chegadas
                ->whereBetween('departure_datetime', [$start, $end]);

            $results = $query->orderBy('departure_datetime', 'desc')->paginate(15);
        }


        // Define o título dinamicamente
        $title = ($this->reportType === 'particular' || $this->reportType === 'private')
            ? 'Meu Relatório - Veículos Particulares'
            : 'Meu Relatório - Frota Oficial';

        // Cria a string do período
        $period = 'Período: ' . $start->format('d/m/Y') . ' a ' . $end->format('d/m/Y');

        // Define o cabeçalho da página
        $header = ($this->reportType === 'particular' || $this->reportType === 'private')
            ? 'Meu Relatório Pessoal - Particulares'
            : 'Meu Relatório Pessoal - Oficiais';


        return view('livewire.personal-report', [
            'results' => $results,
            'porteiroName' => $porteiroName, // Ainda útil para exibir na view
            'period' => $period,
            'title' => $title, // Título interno (pode ser usado na view)
        ])->layoutData(['header' => $header]); // Define o cabeçalho da página do layout
    }
}