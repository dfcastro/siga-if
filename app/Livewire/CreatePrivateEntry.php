<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\PrivateEntry; // Importamos nosso Modelo
use Livewire\Attributes\Rule;

class CreatePrivateEntry extends Component
{
    // Variáveis públicas que estão ligadas aos campos do formulário via wire:model
    #[Rule('required|min:2', message: 'O modelo do veículo é obrigatório.')]
    public string $vehicle_model = '';

    #[Rule('required|min:7', message: 'A placa é obrigatória e deve ter no mínimo 7 caracteres.')]
    public string $license_plate = '';

    #[Rule('required', message: 'O motivo da entrada é obrigatório.')]
    public string $entry_reason = '';


    // Função que será chamada quando o formulário for enviado
    public function save()
    {
        $this->validate(); // Executa as regras que definimos acima

        // Se a validação passar, o código continua e salva os dados
        PrivateEntry::create([
            'vehicle_model'   => $this->vehicle_model,
            'license_plate'   => $this->license_plate,
            'entry_reason'    => $this->entry_reason,
            'entry_at'        => now(),
            'guard_on_entry'  => 'Porteiro Teste',
        ]);

        session()->flash('success', 'Entrada registrada com sucesso!');
        $this->reset();
    }

    public function render()
    {
        // 1. Busca no banco de dados os registros onde 'exit_at' é nulo.
        // 2. Ordena pelos mais recentes primeiro ('latest').
        // 3. Pega todos os resultados ('get').
        $currentVehicles = PrivateEntry::whereNull('exit_at')
            ->latest('entry_at')
            ->get();

        // Envia a variável $currentVehicles para a view
        return view('livewire.create-private-entry', [
            'currentVehicles' => $currentVehicles,
        ]);
    }

    public function registerExit($entryId)
    {
        // 1. Encontra o registro específico pelo ID que recebemos
        $entry = PrivateEntry::find($entryId);

        // 2. Verifica se o registro realmente existe antes de continuar
        if ($entry) {
            // 3. Atualiza os campos de saída
            $entry->exit_at = now(); // Pega a data e hora atuais
            $entry->guard_on_exit = 'Porteiro Teste Saída'; // Provisório

            // 4. Salva as alterações no banco de dados
            $entry->save();

            // 5. (Opcional) Envia uma mensagem de sucesso
            session()->flash('success', 'Saída do veículo ' . $entry->license_plate . ' registrada com sucesso!');
        }
    }
}
