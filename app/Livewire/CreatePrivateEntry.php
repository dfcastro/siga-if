<?php

namespace App\Livewire;

use App\Models\Driver;
use App\Models\PrivateEntry;
use App\Models\Vehicle;
use Livewire\Component;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class CreatePrivateEntry extends Component
{
    // Propriedades para o formulário
    public string $license_plate = '';
    public string $vehicle_model = '';
    public string $entry_reason = '';
    public $selected_driver_id = ''; // ID do motorista que está dirigindo
    public bool $isNewVisitor = false;
    public string $visitor_document = '';
    public string $other_reason = '';
    public array $predefinedReasons = [
        'Entrada de Servidor',
        'Reunião',
        'Entrega de Material',
        'Visita Técnica',
        'Evento',
        'Prestação de Serviço',
        'Pais de aluno, buscar aluno, trazer aluno,etc',
    ];
    public string $successMessage = '';
    public string $exitSearch = '';

    // Propriedades para a busca
    public string $search = '';
    public $searchResults = [];
    public $selectedVehicleId = null; // ID do veículo pré-cadastrado, se aplicável


    public function render()
    {
        // ATUALIZAÇÃO na busca de veículos no pátio
        $currentVehicles = PrivateEntry::with(['vehicle.driver', 'driver'])
            ->whereNull('exit_at')
            // O when() só executa a busca se a propriedade $exitSearch não estiver vazia
            ->when($this->exitSearch, function ($query) {
                $searchTerm = '%' . $this->exitSearch . '%';
                // Procura na placa do registro
                $query->where('license_plate', 'like', $searchTerm)
                    // OU procura no nome do motorista relacionado
                    ->orWhereHas('driver', function ($subQuery) use ($searchTerm) {
                        $subQuery->where('name', 'like', $searchTerm);
                    });
            })
            ->latest('entry_at')
            ->get();

        $drivers = Driver::orderBy('name')->get();

        return view('livewire.create-private-entry', [
            'currentVehicles' => $currentVehicles,
            'drivers' => $drivers
        ]);
    }


    // Método mágico que roda sempre que a propriedade 'search' é atualizada
    // Método mágico que roda sempre que a propriedade 'search' é atualizada
    public function updatedSearch($value)
    {
        // Se a busca for curta, limpamos os resultados
        if (strlen($value) < 3) {
            $this->searchResults = collect(); // Usamos uma coleção vazia
            return;
        }

        // 1. Busca por veículos (placa ou modelo)
        $vehiclesFound = Vehicle::with('driver')
            ->where('license_plate', 'like', '%' . $value . '%')
            ->orWhere('model', 'like', '%' . $value . '%')
            ->get();

        // 2. Busca por motoristas (pelo nome)
        $driversFound = Driver::with('vehicles')
            ->where('name', 'like', '%' . $value . '%')
            ->get();

        // 3. Formata e une os resultados
        $formattedResults = collect();

        // Adiciona os veículos encontrados à lista
        foreach ($vehiclesFound as $vehicle) {
            $formattedResults->push([
                'id' => $vehicle->id,
                'text' => "VEÍCULO: {$vehicle->license_plate} ({$vehicle->model}) - Prop.: {$vehicle->driver->name}"
            ]);
        }

        // Adiciona os veículos dos motoristas encontrados à lista
        foreach ($driversFound as $driver) {
            foreach ($driver->vehicles as $vehicle) {
                $formattedResults->push([
                    'id' => $vehicle->id, // O ID é sempre do veículo que será selecionado
                    'text' => "MOTORISTA: {$driver->name} - Veículo: {$vehicle->license_plate} ({$vehicle->model})"
                ]);
            }
        }

        // Remove duplicatas (caso um veículo seja encontrado nas duas buscas) e ordena
        $this->searchResults = $formattedResults->unique('id')->sortBy('text');
    }

    // Método chamado quando um veículo da lista de busca é selecionado
    public function selectVehicle($vehicleId)
    {
        $vehicle = Vehicle::findOrFail($vehicleId);
        $this->selectedVehicleId = $vehicle->id;
        $this->license_plate = $vehicle->license_plate;
        $this->vehicle_model = $vehicle->model;
        $this->selected_driver_id = $vehicle->driver_id;

        // Limpa a busca
        $this->search = '';
        $this->searchResults = [];

        // AVISA O JAVASCRIPT para atualizar o seletor com o ID do motorista
        $this->dispatch('set-driver-select', $this->selected_driver_id);
    }

    // Método para salvar a entrada
    public function updatedSelectedDriverId($value)
    {
        // Se o valor não for um número, significa que é um nome novo (visitante)
        if (!is_numeric($value) && !empty($value)) {
            $this->isNewVisitor = true;
        } else {
            $this->isNewVisitor = false;
        }
    }

    public function save()
    {
        // 1. Unificamos todas as regras de validação aqui primeiro
        $this->validate([
            'license_plate' => ['required', 'min:7', Rule::unique('private_entries')->whereNull('exit_at')],
            'vehicle_model' => 'required|min:2',
            'selected_driver_id' => 'required', // Apenas verificamos se algo foi selecionado ou digitado
            'entry_reason' => 'required',
            'other_reason' => 'required_if:entry_reason,Outro',
            'visitor_document' => ['required_if:isNewVisitor,true', 'nullable', 'unique:drivers,document'],
        ], [
            'license_plate.unique' => 'Esta placa já consta como dentro do campus.',
            'other_reason.required_if' => 'Por favor, especifique o motivo da entrada.',
            'visitor_document.required_if' => 'O CPF do visitante é obrigatório.',
            'visitor_document.unique' => 'Este CPF já está cadastrado.'
        ]);

        // Se a validação completa passou, podemos prosseguir com segurança
        $driverId = $this->selected_driver_id;
        $finalReason = ($this->entry_reason === 'Outro') ? $this->other_reason : $this->entry_reason;

        // 2. Verificamos se é um novo visitante DEPOIS da validação
        if ($this->isNewVisitor) {
            $newDriver = Driver::create([
                'name' => $this->selected_driver_id,
                'document' => $this->visitor_document,
                'type' => 'Visitante',
                'is_authorized' => false,
            ]);
            // Usamos o ID do motorista que acabamos de criar
            $driverId = $newDriver->id;
        }

        // 3. Finalmente, criamos o registro da entrada com as variáveis corretas
        PrivateEntry::create([
            'license_plate'   => $this->license_plate,
            'vehicle_model'   => $this->vehicle_model,
            'entry_reason'    => $finalReason,
            'entry_at'        => now(),
            'guard_on_entry'  => auth()->user()->name,
            'vehicle_id'      => $this->selectedVehicleId,
            'driver_id'       => $driverId, // Usamos a variável $driverId correta
        ]);

        // 4. Usamos a propriedade $this->license_plate que já temos para a mensagem
        $this->successMessage = 'Entrada do veículo ' . $this->license_plate . ' registrada com sucesso!';

        $this->resetForm();
    }

    public function resetForm()
    {
        // Apenas removemos 'successMessage' da lista
        $this->reset(['license_plate', 'vehicle_model', 'entry_reason', 'other_reason', 'search', 'searchResults', 'selectedVehicleId', 'selected_driver_id', 'isNewVisitor', 'visitor_document']);
        $this->dispatch('reset-form-fields');
    }

    public function registerExit($entryId)
    {
        $entry = PrivateEntry::find($entryId);
        if ($entry) {
            $entry->exit_at = now();
            $entry->guard_on_exit = auth()->user()->name;
            $entry->save();

            // TROQUE a linha da session()->flash() por esta:
            $this->successMessage = 'Saída do veículo ' . ($entry->vehicle->license_plate ?? $entry->license_plate) . ' registrada com sucesso!';
        }
    }
}
