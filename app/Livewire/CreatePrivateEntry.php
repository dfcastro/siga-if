<?php

namespace App\Livewire;

use App\Models\Driver;
use App\Models\PrivateEntry;
use App\Models\Vehicle;
use Livewire\Component;
use Illuminate\Validation\Rule;

class CreatePrivateEntry extends Component
{
    // Propriedades para o formulário
    public string $license_plate = '';
    public string $vehicle_model = '';
    public string $entry_reason = '';
    public $selected_driver_id = ''; // ID do motorista que está dirigindo

    // Propriedades para a busca
    public string $search = '';
    public $searchResults = [];
    public $selectedVehicleId = null; // ID do veículo pré-cadastrado, se aplicável

    public function render()
    {
        $currentVehicles = PrivateEntry::with(['vehicle.driver', 'driver'])->whereNull('exit_at')->latest('entry_at')->get();
        $drivers = Driver::orderBy('name')->get(); // Para o select de motorista de visitante

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
    public function save()
    {
        // Verifica se o valor recebido é um texto (novo motorista) ou um número (ID existente)
        if (!is_numeric($this->selected_driver_id)) {
            // Se for um texto, cria um novo motorista
            $newDriver = Driver::create([
                'name' => $this->selected_driver_id, // O valor é o próprio nome
                'document' => 'VISITANTE-' . now()->timestamp, // Gera um documento único
                'type' => 'Visitante',
            ]);
            // Atualiza a propriedade com o ID do motorista recém-criado
            $this->selected_driver_id = $newDriver->id;
        }

        $this->validate([
            // REGRA ATUALIZADA AQUI
            'license_plate' => [
                'required',
                'min:7',
                // A placa deve ser única na tabela 'private_entries' ONDE a coluna 'exit_at' é NULA.
                Rule::unique('private_entries')->whereNull('exit_at')
            ],
            'vehicle_model' => 'required|min:2',
            'entry_reason' => 'required',
            'selected_driver_id' => 'required|exists:drivers,id',
        ], [
            // Mensagem de erro customizada para a nova regra
            'license_plate.unique' => 'Esta placa já consta como dentro do campus. É preciso registrar a saída antes de uma nova entrada.'
        ]);

        PrivateEntry::create([
            'license_plate'   => $this->license_plate,
            'vehicle_model'   => $this->vehicle_model,
            'entry_reason'    => $this->entry_reason,
            'entry_at'        => now(),
            'guard_on_entry'  => 'Porteiro IFNMG',
            'vehicle_id'      => $this->selectedVehicleId,
            'driver_id'       => $this->selected_driver_id,
        ]);

        session()->flash('success', 'Entrada registrada com sucesso!');
        $this->resetForm();
    }

    // Limpa o formulário
    public function resetForm()
    {
        $this->reset(['license_plate', 'vehicle_model', 'entry_reason', 'search', 'searchResults', 'selectedVehicleId', 'selected_driver_id']);
        // Envia o evento para o JavaScript limpar o Tom Select
        $this->dispatch('reset-form-fields');
    }

    public function registerExit($entryId)
    {
        $entry = PrivateEntry::find($entryId);
        if ($entry) {
            $entry->exit_at = now();
            $entry->guard_on_exit = 'Porteiro IFNMG';
            $entry->save();
            session()->flash('success', 'Saída registrada com sucesso!');
        }
    }
}
