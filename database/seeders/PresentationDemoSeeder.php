<?php

namespace Database\Seeders;

use App\Models\Driver;
use App\Models\OfficialTrip;
use App\Models\PrivateEntry;
use App\Models\ReportSubmission;
use App\Models\User;
use App\Models\Vehicle;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PresentationDemoSeeder extends Seeder
{
    private const PASSWORD = 'Demo@2026';

    private const USER_EMAILS = [
        'porteiro.demo@siga.local',
        'porteiro.apoio@siga.local',
        'fiscal.demo@siga.local',
    ];

    private const DRIVER_DOCS = [
        '90000000001',
        '90000000002',
        '90000000003',
        '90000000004',
        '90000000005',
    ];

    private const PLATES = [
        'DME-1001',
        'DME-1002',
        'DME-1003',
        'OFI-2001',
        'OFI-2002',
    ];

    public function run(): void
    {
        if (app()->environment('production')) {
            throw new \RuntimeException('O PresentationDemoSeeder não deve ser executado em produção.');
        }

        DB::transaction(function () {
            $this->cleanupPreviousDemo();

            $guard = User::create([
                'name' => 'Porteiro Demo',
                'email' => 'porteiro.demo@siga.local',
                'password' => self::PASSWORD,
                'role' => 'porteiro',
                'fiscal_type' => null,
                'email_verified_at' => now(),
            ]);

            $supportGuard = User::create([
                'name' => 'Porteiro Apoio Demo',
                'email' => 'porteiro.apoio@siga.local',
                'password' => self::PASSWORD,
                'role' => 'porteiro',
                'fiscal_type' => null,
                'email_verified_at' => now(),
            ]);

            $fiscal = User::create([
                'name' => 'Fiscal Demo',
                'email' => 'fiscal.demo@siga.local',
                'password' => self::PASSWORD,
                'role' => 'fiscal',
                'fiscal_type' => 'both',
                'email_verified_at' => now(),
            ]);

            $drivers = [
                Driver::create(['name' => 'Carlos Henrique Demo', 'document' => '90000000001', 'telefone' => '(33) 99999-1001', 'type' => 'Servidor', 'is_authorized' => true]),
                Driver::create(['name' => 'Marina Alves Demo', 'document' => '90000000002', 'telefone' => '(33) 99999-1002', 'type' => 'Servidor', 'is_authorized' => true]),
                Driver::create(['name' => 'José Roberto Demo', 'document' => '90000000003', 'telefone' => '(33) 99999-1003', 'type' => 'Visitante', 'is_authorized' => false]),
                Driver::create(['name' => 'Ana Paula Demo', 'document' => '90000000004', 'telefone' => '(33) 99999-1004', 'type' => 'Terceirizado', 'is_authorized' => true]),
                Driver::create(['name' => 'Roberto Lima Demo', 'document' => '90000000005', 'telefone' => '(33) 99999-1005', 'type' => 'Servidor', 'is_authorized' => true]),
            ];

            $private1 = Vehicle::create(['license_plate' => 'DME-1001', 'model' => 'Fiat Argo Demo', 'color' => 'Branco', 'type' => 'Particular', 'driver_id' => null]);
            $private2 = Vehicle::create(['license_plate' => 'DME-1002', 'model' => 'Chevrolet Onix Demo', 'color' => 'Prata', 'type' => 'Particular', 'driver_id' => null]);
            $private3 = Vehicle::create(['license_plate' => 'DME-1003', 'model' => 'Honda City Demo', 'color' => 'Preto', 'type' => 'Particular', 'driver_id' => null]);
            $official1 = Vehicle::create(['license_plate' => 'OFI-2001', 'model' => 'Toyota Corolla Oficial Demo', 'color' => 'Branco', 'type' => 'Oficial', 'driver_id' => null]);
            $official2 = Vehicle::create(['license_plate' => 'OFI-2002', 'model' => 'Fiat Toro Oficial Demo', 'color' => 'Branco', 'type' => 'Oficial', 'driver_id' => null]);

            $private1->drivers()->sync([$drivers[0]->id, $drivers[1]->id, $drivers[2]->id]);
            $private2->drivers()->sync([$drivers[1]->id, $drivers[3]->id]);
            $private3->drivers()->sync([$drivers[2]->id]);
            $official1->drivers()->sync([$drivers[0]->id, $drivers[4]->id]);
            $official2->drivers()->sync([$drivers[1]->id, $drivers[4]->id]);

            $privateVehicles = [$private1, $private2, $private3];
            $officialVehicles = [$official1, $official2];

            // -----------------------------------------------------------------
            // HISTÓRICO: meses -8 até -3, todos já aprovados.
            // Em agosto/2026: dezembro/2025 a maio/2026.
            // -----------------------------------------------------------------
            $odometer = [
                $official1->id => 15000,
                $official2->id => 28000,
            ];

            for ($monthsAgo = 8; $monthsAgo >= 3; $monthsAgo--) {
                $month = now()->subMonths($monthsAgo)->startOfMonth();
                $nextMonth = $month->copy()->addMonth()->startOfMonth();

                $privateSubmission = ReportSubmission::create([
                    'guard_id' => $guard->id,
                    'fiscal_id' => $fiscal->id,
                    'assigned_fiscal_id' => $fiscal->id,
                    'vehicle_id' => null,
                    'start_date' => $month->copy()->startOfMonth(),
                    'end_date' => $month->copy()->endOfMonth(),
                    'type' => 'private',
                    'status' => 'approved',
                    'submitted_at' => $nextMonth->copy()->addDay()->setTime(8, 10 + ($monthsAgo % 4) * 5),
                    'approved_at' => $nextMonth->copy()->addDay()->setTime(10, 20 + ($monthsAgo % 3) * 10),
                ]);

                $privateCount = 4 + (($monthsAgo + 1) % 4); // 4 a 7 registros por mês
                $reasons = [
                    'Entrada de Servidor',
                    'Reunião',
                    'Entrega de Material',
                    'Visita Técnica',
                    'Evento',
                    'Prestação de Serviço',
                    'Pais de aluno, buscar aluno, trazer aluno,etc',
                ];

                for ($i = 0; $i < $privateCount; $i++) {
                    $entryGuard = (($i + $monthsAgo) % 4 === 0) ? $supportGuard : $guard;
                    $vehicle = $privateVehicles[($i + $monthsAgo) % count($privateVehicles)];
                    $driver = $drivers[($i + $monthsAgo) % 4];
                    $day = min(3 + ($i * 4), 26);

                    $this->privateEntry(
                        $vehicle,
                        $driver,
                        $month->copy()->addDays($day)->setTime(7 + ($i % 5), 10 + (($i * 7) % 45)),
                        $entryGuard,
                        $guard,
                        $reasons[($i + $monthsAgo) % count($reasons)],
                        $privateSubmission->id
                    );
                }

                // Alterna os veículos oficiais ao longo do histórico e,
                // em alguns meses, cria relatório para os dois veículos.
                $vehiclesForMonth = ($monthsAgo % 3 === 0)
                    ? $officialVehicles
                    : [$officialVehicles[$monthsAgo % 2]];

                foreach ($vehiclesForMonth as $vehicleIndex => $officialVehicle) {
                    $officialSubmission = ReportSubmission::create([
                        'guard_id' => $guard->id,
                        'fiscal_id' => $fiscal->id,
                        'assigned_fiscal_id' => $fiscal->id,
                        'vehicle_id' => $officialVehicle->id,
                        'start_date' => $month->copy()->startOfMonth(),
                        'end_date' => $month->copy()->endOfMonth(),
                        'observation' => 'Relatório histórico aprovado para demonstração.',
                        'type' => 'official',
                        'status' => 'approved',
                        'submitted_at' => $nextMonth->copy()->addDay()->setTime(8, 30 + ($vehicleIndex * 10)),
                        'approved_at' => $nextMonth->copy()->addDay()->setTime(10, 50 + ($vehicleIndex * 5)),
                    ]);

                    $tripCount = 1 + (($monthsAgo + $vehicleIndex) % 3); // 1 a 3 viagens
                    $destinations = [
                        'Pedra Azul - MG',
                        'Teófilo Otoni - MG',
                        'Montes Claros - MG',
                        'Salinas - MG',
                        'Araçuaí - MG',
                        'Itaobim - MG',
                        'Jequitinhonha - MG',
                        'Jacinto - MG',
                    ];

                    for ($i = 0; $i < $tripCount; $i++) {
                        $departureKm = $odometer[$officialVehicle->id];
                        $distance = 105 + (($monthsAgo * 19 + $i * 37 + $vehicleIndex * 23) % 170);
                        $arrivalKm = $departureKm + $distance;
                        $departureGuard = (($i + $monthsAgo) % 3 === 0) ? $supportGuard : $guard;
                        // Alterna somente entre os motoristas realmente vinculados a cada veículo oficial.
                        $driver = $officialVehicle->id === $official1->id
                            ? $drivers[(($i + $monthsAgo) % 2) === 0 ? 0 : 4]
                            : $drivers[(($i + $monthsAgo) % 2) === 0 ? 1 : 4];

                        $this->officialTrip(
                            $officialVehicle,
                            $driver,
                            $month->copy()->addDays(min(4 + ($i * 9), 25))->setTime(6 + ($i % 3), 40),
                            $departureKm,
                            $arrivalKm,
                            $destinations[($monthsAgo + $i + $vehicleIndex) % count($destinations)],
                            $departureGuard,
                            $guard,
                            $officialSubmission->id
                        );

                        $odometer[$officialVehicle->id] = $arrivalKm;
                    }
                }
            }

            // -----------------------------------------------------------------
            // MÊS -2: relatórios já submetidos, aguardando visto.
            // Em agosto/2026: junho/2026.
            // -----------------------------------------------------------------
            $m2 = now()->subMonths(2)->startOfMonth();
            $m1 = now()->subMonth()->startOfMonth();

            $pendingPrivate = ReportSubmission::create([
                'guard_id' => $guard->id,
                'fiscal_id' => null,
                'assigned_fiscal_id' => $fiscal->id,
                'vehicle_id' => null,
                'start_date' => $m2->copy()->startOfMonth(),
                'end_date' => $m2->copy()->endOfMonth(),
                'type' => 'private',
                'status' => 'pending',
                'submitted_at' => $m1->copy()->addDay()->setTime(8, 5),
                'approved_at' => null,
            ]);

            for ($i = 0; $i < 7; $i++) {
                $entryGuard = in_array($i, [0, 4], true) ? $supportGuard : $guard;
                $this->privateEntry(
                    $privateVehicles[$i % 3],
                    $drivers[$i % 4],
                    $m2->copy()->addDays(2 + ($i * 4))->setTime(8 + ($i % 4), 15 + (($i * 5) % 40)),
                    $entryGuard,
                    $guard,
                    ['Entrada de Servidor', 'Reunião', 'Evento', 'Entrega de Material', 'Visita Técnica', 'Prestação de Serviço', 'Reunião'][$i],
                    $pendingPrivate->id
                );
            }

            foreach ($officialVehicles as $index => $officialVehicle) {
                $pendingOfficial = ReportSubmission::create([
                    'guard_id' => $guard->id,
                    'fiscal_id' => null,
                    'assigned_fiscal_id' => $fiscal->id,
                    'vehicle_id' => $officialVehicle->id,
                    'start_date' => $m2->copy()->startOfMonth(),
                    'end_date' => $m2->copy()->endOfMonth(),
                    'observation' => $index === 0 ? 'Conferir deslocamentos administrativos.' : 'Conferir viagens institucionais do período.',
                    'type' => 'official',
                    'status' => 'pending',
                    'submitted_at' => $m1->copy()->addDay()->setTime(8, 20 + ($index * 10)),
                    'approved_at' => null,
                ]);

                for ($i = 0; $i < 2; $i++) {
                    $departureKm = $odometer[$officialVehicle->id];
                    $arrivalKm = $departureKm + 135 + ($index * 45) + ($i * 38);
                    $this->officialTrip(
                        $officialVehicle,
                        $officialVehicle->id === $official1->id ? $drivers[$i === 0 ? 0 : 4] : $drivers[$i === 0 ? 1 : 4],
                        $m2->copy()->addDays(5 + ($i * 14) + ($index * 2))->setTime(7 + $i, 10),
                        $departureKm,
                        $arrivalKm,
                        ['Montes Claros - MG', 'Salinas - MG', 'Araçuaí - MG', 'Pedra Azul - MG'][($index * 2) + $i],
                        $i === 1 ? $supportGuard : $guard,
                        $guard,
                        $pendingOfficial->id
                    );
                    $odometer[$officialVehicle->id] = $arrivalKm;
                }
            }

            // -----------------------------------------------------------------
            // MÊS -1: registros prontos para serem submetidos AO VIVO.
            // Em agosto/2026: julho/2026.
            // -----------------------------------------------------------------
            $privateReasons = [
                'Entrada de Servidor',
                'Reunião',
                'Entrega de Material',
                'Visita Técnica',
                'Evento',
                'Prestação de Serviço',
                'Pais de aluno, buscar aluno, trazer aluno,etc',
                'Transporte de Alunos (Ônibus/Vans) | Obs: Grupo de 22 estudantes',
                'Reunião',
                'Entrega de Material',
            ];

            for ($i = 0; $i < 10; $i++) {
                $entryGuard = in_array($i, [1, 4, 8], true) ? $supportGuard : $guard;
                $this->privateEntry(
                    $privateVehicles[$i % 3],
                    $drivers[$i % 4],
                    $m1->copy()->addDays(1 + ($i * 2))->setTime(7 + ($i % 5), 10 + (($i * 3) % 45)),
                    $entryGuard,
                    $guard,
                    $privateReasons[$i],
                    null
                );
            }

            // Finalizado por outro porteiro: NÃO aparece no relatório do Porteiro Demo.
            $this->privateEntry($private2, $drivers[3], $m1->copy()->addDays(22)->setTime(14, 0), $guard, $supportGuard, 'Reunião', null);

            // Entrada ainda aberta: NÃO aparece na submissão.
            PrivateEntry::create([
                'driver_id' => $drivers[2]->id,
                'vehicle_id' => $private3->id,
                'license_plate' => $private3->license_plate,
                'vehicle_model' => $private3->model,
                'entry_reason' => 'Visita Técnica - registro propositalmente em aberto',
                'entry_at' => $m1->copy()->addDays(25)->setTime(15, 10),
                'exit_at' => null,
                'guard_on_entry_id' => $guard->id,
                'guard_on_exit_id' => null,
                'report_submission_id' => null,
            ]);

            // Viagens oficiais prontas para submissão ao vivo.
            foreach ($officialVehicles as $index => $officialVehicle) {
                $destinations = $index === 0
                    ? ['Jequitinhonha - MG', 'Pedra Azul - MG', 'Itaobim - MG']
                    : ['Araçuaí - MG', 'Jacinto - MG', 'Almenara - rota externa demo'];

                for ($i = 0; $i < 3; $i++) {
                    $departureKm = $odometer[$officialVehicle->id];
                    $arrivalKm = $departureKm + 110 + ($index * 35) + ($i * 42);
                    $arrivalGuard = ($index === 1 && $i === 2) ? $supportGuard : $guard;
                    $departureGuard = ($i === 0) ? $supportGuard : $guard;

                    $this->officialTrip(
                        $officialVehicle,
                        $officialVehicle->id === $official1->id ? $drivers[$i % 2 === 0 ? 0 : 4] : $drivers[$i % 2 === 0 ? 1 : 4],
                        $m1->copy()->addDays(3 + ($i * 9) + ($index * 3))->setTime(7 + ($i % 2), 0 + ($i * 10)),
                        $departureKm,
                        $arrivalKm,
                        $destinations[$i],
                        $departureGuard,
                        $arrivalGuard,
                        null
                    );
                    $odometer[$officialVehicle->id] = $arrivalKm;
                }
            }

            // Viagem sem chegada: NÃO aparece na submissão.
            OfficialTrip::create([
                'vehicle_id' => $official1->id,
                'driver_id' => $drivers[0]->id,
                'destination' => 'Viagem em aberto - demonstração',
                'passengers' => 'Servidor Demo',
                'return_observation' => 'Registro propositalmente sem chegada.',
                'departure_datetime' => $m1->copy()->addDays(28)->setTime(13, 0),
                'departure_odometer' => $odometer[$official1->id],
                'arrival_datetime' => null,
                'arrival_odometer' => null,
                'guard_on_departure_id' => $guard->id,
                'guard_on_arrival_id' => null,
                'report_submission_id' => null,
            ]);
        });

        $this->command?->newLine();
        $this->command?->info('Base de apresentação do SIGA-IF criada com sucesso.');
        $this->command?->line('Histórico criado: 8 meses anteriores, com múltiplos cenários de relatório.');
        $this->command?->line('Porteiro: porteiro.demo@siga.local / ' . self::PASSWORD);
        $this->command?->line('Fiscal:   fiscal.demo@siga.local / ' . self::PASSWORD);
        $this->command?->line('Apoio:    porteiro.apoio@siga.local / ' . self::PASSWORD);
    }

    private function privateEntry(Vehicle $vehicle, Driver $driver, Carbon $entryAt, User $entryGuard, User $exitGuard, string $reason, ?int $submissionId): PrivateEntry
    {
        return PrivateEntry::create([
            'driver_id' => $driver->id,
            'vehicle_id' => $vehicle->id,
            'license_plate' => $vehicle->license_plate,
            'vehicle_model' => $vehicle->model,
            'entry_reason' => $reason,
            'entry_at' => $entryAt,
            'exit_at' => $entryAt->copy()->addMinutes(45 + (($entryAt->day % 4) * 25)),
            'guard_on_entry_id' => $entryGuard->id,
            'guard_on_exit_id' => $exitGuard->id,
            'report_submission_id' => $submissionId,
        ]);
    }

    private function officialTrip(Vehicle $vehicle, Driver $driver, Carbon $departureAt, int $departureKm, int $arrivalKm, string $destination, User $departureGuard, User $arrivalGuard, ?int $submissionId): OfficialTrip
    {
        return OfficialTrip::create([
            'vehicle_id' => $vehicle->id,
            'driver_id' => $driver->id,
            'destination' => $destination,
            'passengers' => 'Equipe institucional - demonstração',
            'return_observation' => 'Retorno concluído sem intercorrências.',
            'departure_datetime' => $departureAt,
            'departure_odometer' => $departureKm,
            'arrival_datetime' => $departureAt->copy()->addHours(5)->addMinutes(30),
            'arrival_odometer' => $arrivalKm,
            'guard_on_departure_id' => $departureGuard->id,
            'guard_on_arrival_id' => $arrivalGuard->id,
            'report_submission_id' => $submissionId,
        ]);
    }

    private function cleanupPreviousDemo(): void
    {
        $userIds = User::whereIn('email', self::USER_EMAILS)->pluck('id');
        $vehicleIds = Vehicle::withTrashed()->whereIn('license_plate', self::PLATES)->pluck('id');
        $driverIds = Driver::withTrashed()->whereIn('document', self::DRIVER_DOCS)->pluck('id');

        if ($userIds->isNotEmpty() || $vehicleIds->isNotEmpty()) {
            $submissionIds = ReportSubmission::query()
                ->whereIn('guard_id', $userIds)
                ->orWhereIn('vehicle_id', $vehicleIds)
                ->pluck('id');

            PrivateEntry::whereIn('report_submission_id', $submissionIds)->update(['report_submission_id' => null]);
            OfficialTrip::whereIn('report_submission_id', $submissionIds)->update(['report_submission_id' => null]);
            ReportSubmission::whereIn('id', $submissionIds)->delete();

            PrivateEntry::whereIn('vehicle_id', $vehicleIds)->delete();
            OfficialTrip::whereIn('vehicle_id', $vehicleIds)->delete();
        }

        if ($vehicleIds->isNotEmpty() || $driverIds->isNotEmpty()) {
            DB::table('driver_vehicle')
                ->whereIn('vehicle_id', $vehicleIds)
                ->orWhereIn('driver_id', $driverIds)
                ->delete();
        }

        Vehicle::withTrashed()->whereIn('license_plate', self::PLATES)->forceDelete();
        Driver::withTrashed()->whereIn('document', self::DRIVER_DOCS)->forceDelete();
        User::whereIn('email', self::USER_EMAILS)->delete();
    }
}
