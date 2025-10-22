<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\OfficialTrip;
use App\Models\PrivateEntry;
use App\Models\User;
use Illuminate\Support\Facades\DB; // Import DB facade

class BackfillGuardIds extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backfill:guard-ids'; // Nome do comando para executar

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Preenche as colunas guard_..._id com base nos nomes existentes nas tabelas official_trips e private_entries';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Iniciando o preenchimento dos IDs dos porteiros...');

        // Cache de usuários para evitar múltiplas buscas pelo mesmo nome
        $userCache = [];
        $getUserByName = function ($name) use (&$userCache) {
            if (empty($name)) {
                return null;
            }
            if (!isset($userCache[$name])) {
                // Busca o ID do usuário pelo nome, ignorando maiúsculas/minúsculas e espaços extras
                $userCache[$name] = User::where(DB::raw('TRIM(UPPER(name))'), '=', trim(strtoupper($name)))->value('id');
            }
            return $userCache[$name];
        };

        $processedTrips = 0;
        $updatedDepartureIds = 0;
        $updatedArrivalIds = 0;
        $notFoundDepartureNames = [];
        $notFoundArrivalNames = [];

        $this->info('Processando Viagens Oficiais (official_trips)...');
        // Processa em chunks para não sobrecarregar a memória
        OfficialTrip::whereNull('guard_on_departure_id')
            ->orWhereNull('guard_on_arrival_id')
            ->chunkById(200, function ($trips) use (
                $getUserByName,
                &$processedTrips,
                &$updatedDepartureIds,
                &$updatedArrivalIds,
                &$notFoundDepartureNames,
                &$notFoundArrivalNames
            ) {
                foreach ($trips as $trip) {
                    $departureId = $getUserByName($trip->guard_on_departure);
                    $arrivalId = $getUserByName($trip->guard_on_arrival);
                    $updateData = [];

                    if ($trip->guard_on_departure && is_null($trip->guard_on_departure_id)) {
                         if ($departureId) {
                            $updateData['guard_on_departure_id'] = $departureId;
                            $updatedDepartureIds++;
                         } elseif (!in_array($trip->guard_on_departure, $notFoundDepartureNames)) {
                            $notFoundDepartureNames[] = $trip->guard_on_departure;
                         }
                    }

                    if ($trip->guard_on_arrival && is_null($trip->guard_on_arrival_id)) {
                         if ($arrivalId) {
                            $updateData['guard_on_arrival_id'] = $arrivalId;
                            $updatedArrivalIds++;
                         } elseif (!in_array($trip->guard_on_arrival, $notFoundArrivalNames)) {
                            $notFoundArrivalNames[] = $trip->guard_on_arrival;
                         }
                    }

                    if (!empty($updateData)) {
                        $trip->update($updateData);
                    }
                    $processedTrips++;
                }
                $this->output->write('.'); // Mostra progresso
            });

        $this->info("\nViagens Oficiais Processadas: {$processedTrips}");
        $this->info("IDs de Partida Atualizados: {$updatedDepartureIds}");
        $this->info("IDs de Chegada Atualizados: {$updatedArrivalIds}");
        if (!empty($notFoundDepartureNames)) {
            $this->warn("Nomes de Partida não encontrados na tabela users: " . implode(', ', $notFoundDepartureNames));
        }
         if (!empty($notFoundArrivalNames)) {
            $this->warn("Nomes de Chegada não encontrados na tabela users: " . implode(', ', $notFoundArrivalNames));
        }

        // --- Processamento para Private Entries ---
        $processedEntries = 0;
        $updatedEntryIds = 0;
        $updatedExitIds = 0;
        $notFoundEntryNames = [];
        $notFoundExitNames = [];

        $this->info("\nProcessando Entradas Particulares (private_entries)...");
        PrivateEntry::whereNull('guard_on_entry_id')
            ->orWhereNull('guard_on_exit_id')
            ->chunkById(200, function ($entries) use (
                $getUserByName,
                &$processedEntries,
                &$updatedEntryIds,
                &$updatedExitIds,
                &$notFoundEntryNames,
                &$notFoundExitNames
            ) {
                foreach ($entries as $entry) {
                     $entryId = $getUserByName($entry->guard_on_entry);
                     $exitId = $getUserByName($entry->guard_on_exit);
                     $updateData = [];

                     if ($entry->guard_on_entry && is_null($entry->guard_on_entry_id)) {
                         if ($entryId) {
                            $updateData['guard_on_entry_id'] = $entryId;
                            $updatedEntryIds++;
                         } elseif (!in_array($entry->guard_on_entry, $notFoundEntryNames)) {
                            $notFoundEntryNames[] = $entry->guard_on_entry;
                         }
                     }

                     if ($entry->guard_on_exit && is_null($entry->guard_on_exit_id)) {
                         if ($exitId) {
                            $updateData['guard_on_exit_id'] = $exitId;
                            $updatedExitIds++;
                         } elseif (!in_array($entry->guard_on_exit, $notFoundExitNames)) {
                            $notFoundExitNames[] = $entry->guard_on_exit;
                         }
                     }

                     if (!empty($updateData)) {
                        $entry->update($updateData);
                     }
                     $processedEntries++;
                }
                 $this->output->write('.'); // Mostra progresso
            });

        $this->info("\nEntradas Particulares Processadas: {$processedEntries}");
        $this->info("IDs de Entrada Atualizados: {$updatedEntryIds}");
        $this->info("IDs de Saída Atualizados: {$updatedExitIds}");
        if (!empty($notFoundEntryNames)) {
            $this->warn("Nomes de Entrada não encontrados na tabela users: " . implode(', ', $notFoundEntryNames));
        }
         if (!empty($notFoundExitNames)) {
            $this->warn("Nomes de Saída não encontrados na tabela users: " . implode(', ', $notFoundExitNames));
        }


        $this->info("\nPreenchimento concluído!");
        return Command::SUCCESS;
    }
}