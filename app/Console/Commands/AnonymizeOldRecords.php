<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PrivateEntry;
use App\Models\OfficialTrip;
use Carbon\Carbon;

class AnonymizeOldRecords extends Command
{
    /**
     * A assinatura do comando no console.
     * @var string
     */
    protected $signature = 'app:anonymize-old-records';

    /**
     * A descrição do comando.
     * @var string
     */
    protected $description = 'Anonimiza dados pessoais em registos com mais tempo do que o período de retenção definido, para cumprir com a LGPD.';

    /**
     * Executa a lógica do comando.
     */
    public function handle()
    {
        $this->info('Iniciando o processo de anonimização de registos antigos...');

        // Lê o período de retenção do ficheiro .env. Se não existir, usa 2 anos como padrão.
        $retentionYears = config('app.data_retention_period', 2);
        $retentionLimitDate = Carbon::now()->subYears($retentionYears);
        $anonymizedText = 'Dado Anonimizado (LGPD)';

        $this->line("A anonimizar registos mais antigos que: " . $retentionLimitDate->format('d/m/Y'));

        // 1. Anonimizar Entradas de Veículos Particulares
        $privateEntries = PrivateEntry::where('entry_at', '<', $retentionLimitDate)
            ->whereNotNull('driver_id')
            ->get();

        foreach ($privateEntries as $entry) {
            $entry->driver_id = null;
            $entry->save();
        }
        $this->info(count($privateEntries) . ' registos de entradas particulares foram anonimizados.');

        // 2. Anonimizar Viagens Oficiais
        $officialTrips = OfficialTrip::where('departure_datetime', '<', $retentionLimitDate)
            ->whereNotNull('driver_id')
            ->get();

        foreach ($officialTrips as $trip) {
            $trip->driver_id = null;
            $trip->save();
        }
        $this->info(count($officialTrips) . ' registos de viagens oficiais foram anonimizados.');

        // Adicional: Anonimizar motoristas do tipo "Visitante" que não têm mais registos associados.
        // (Esta é uma melhoria opcional, mas recomendada)

        $this->info('Processo de anonimização concluído com sucesso!');

        return Command::SUCCESS;
    }
}
