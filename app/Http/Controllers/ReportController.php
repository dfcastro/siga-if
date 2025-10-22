<?php

namespace App\Http\Controllers;

use App\Models\OfficialTrip;
use App\Models\PrivateEntry;
use App\Models\Vehicle;
use App\Models\Driver;
use App\Models\ReportSubmission; // Adicionado
use App\Models\User; // Adicionado
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;


class ReportController extends Controller
{
    /**
     * Gera PDF para Viagens Oficiais com base nos filtros.
     */
    public function officialVehiclesPDF(Request $request)
    {
        // Validação (Mantida - Tornar vehicle_id obrigatório para este relatório específico)
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'vehicle_id' => 'required|exists:vehicles,id', // Exige um veículo específico
            'driver_id' => 'nullable|exists:drivers,id',
        ]);

        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);

        // Busca (Mantida)
        $query = OfficialTrip::with([
            'driver' => fn($q) => $q->withTrashed(),
            'vehicle' => fn($q) => $q->withTrashed(),
            'guardDeparture',
            'guardArrival'
        ])
            ->where('vehicle_id', $request->vehicle_id) // Filtra pelo veículo obrigatório
            ->whereBetween('departure_datetime', [$startDate->startOfDay(), $endDate->endOfDay()])
            ->whereNotNull('arrival_datetime');

        if ($request->filled('driver_id')) {
            $query->where('driver_id', $request->driver_id);
        }

        $trips = $query->orderBy('departure_datetime')->get();

        // ### INÍCIO DA LIMPEZA DE DADOS ###
        $trips->transform(function ($trip) {
            // Remove quebras de linha (\n, \r) e limita comprimento
            $trip->destination = Str::limit(preg_replace('/[\r\n]+/', ' ', $trip->destination), 80);
            $trip->passengers = Str::limit(preg_replace('/[\r\n]+/', ' ', $trip->passengers), 80);
            $trip->return_observation = Str::limit(preg_replace('/[\r\n]+/', ' ', $trip->return_observation), 100);

            // Opcional: Limpa nomes relacionados também
            if ($trip->driver) {
                $trip->driver->name = preg_replace('/[\r\n]+/', ' ', $trip->driver->name);
            }
            if ($trip->vehicle) {
                $trip->vehicle->model = preg_replace('/[\r\n]+/', ' ', $trip->vehicle->model);
            }
            if ($trip->guardDeparture) {
                $trip->guardDeparture->name = preg_replace('/[\r\n]+/', ' ', $trip->guardDeparture->name);
            }
            if ($trip->guardArrival) {
                $trip->guardArrival->name = preg_replace('/[\r\n]+/', ' ', $trip->guardArrival->name);
            }

            return $trip;
        });
        // ### FIM DA LIMPEZA DE DADOS ###

        $vehicle = Vehicle::withTrashed()->find($request->vehicle_id); // Agora garantido que existe
        $driver = $request->filled('driver_id') ? Driver::withTrashed()->find($request->driver_id) : null;
        $totalKm = $trips->sum('distance_traveled');

        $data = [
            'vehicle' => $vehicle,
            'driver' => $driver,
            'trips' => $trips, // Dados limpos
            'startDate' => $startDate->format('d/m/Y'), // Formatado para view
            'endDate' => $endDate->format('d/m/Y'),     // Formatado para view
            'totalKm' => $totalKm,
        ];

        // Carrega a view PDF (que já parece correta)
        $pdf = Pdf::loadView('reports.pdf.official', $data);
        $pdf->setPaper('a4', 'landscape');
        $fileName = 'relatorio_oficial_' . Str::slug($vehicle->license_plate ?? 'veiculo') . '_' . $startDate->format('Ym') . '.pdf'; // Nome mais descritivo

        return $pdf->stream($fileName);
    }

    /**
     * Gera PDF para Entradas Particulares com base nos filtros.
     */
    public function privateVehiclesPDF(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'vehicle_id' => 'nullable|exists:vehicles,id',
            'driver_id' => 'nullable|exists:drivers,id',
        ]);

        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);

        $query = PrivateEntry::with([
            'vehicle' => fn($q) => $q->withTrashed(),
            'driver' => fn($q) => $q->withTrashed(),
            'guardEntry',
            'guardExit'
        ])
            ->whereBetween('entry_at', [$startDate->startOfDay(), $endDate->endOfDay()])
            ->whereNotNull('exit_at');

        if ($request->filled('vehicle_id')) {
            $query->where('vehicle_id', $request->vehicle_id);
        }
        if ($request->filled('driver_id')) {
            $query->where('driver_id', $request->driver_id);
        }

        $entries = $query->orderBy('entry_at')->get();

        // Limpeza de Dados (opcional, mas recomendado)
        $entries->transform(function ($entry) {
            $entry->entry_reason = Str::limit(preg_replace('/[\r\n]+/', ' ', $entry->entry_reason), 80);
            if ($entry->driver) {
                $entry->driver->name = preg_replace('/[\r\n]+/', ' ', $entry->driver->name);
            }
            if ($entry->guardExit) {
                $entry->guardExit->name = preg_replace('/[\r\n]+/', ' ', $entry->guardExit->name);
            }
            return $entry;
        });

        $vehicle = $request->filled('vehicle_id') ? Vehicle::withTrashed()->find($request->vehicle_id) : null;
        $driver = $request->filled('driver_id') ? Driver::withTrashed()->find($request->driver_id) : null;

        $data = [
            'entries' => $entries, // Dados limpos
            'startDate' => $startDate->format('d/m/Y'),
            'endDate' => $endDate->format('d/m/Y'),
            'vehicle' => $vehicle,
            'driver' => $driver,
        ];

        $pdf = Pdf::loadView('reports.pdf.private', $data);
        $pdf->setPaper('a4', 'landscape');
        $fileName = 'relatorio_particulares_' . $startDate->format('Ym') . '_' . now()->format('His') . '.pdf';

        return $pdf->stream($fileName);
    }

    /**
     * Gera o relatório pessoal do porteiro logado.
     */
    public function generatePersonalPDF(Request $request)
    {
        $request->validate([
            'type' => 'required|in:private,official',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $porteiroId = Auth::id();
        $porteiroName = Auth::user()->name;
        $reportType = $request->type;
        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);

        $results = collect();

        if ($reportType === 'private') {
            $results = PrivateEntry::with(['vehicle', 'driver', 'guardEntry', 'guardExit'])
                ->where('guard_on_entry_id', $porteiroId) // Ou guard_on_exit_id
                ->whereBetween('entry_at', [$startDate->startOfDay(), $endDate->endOfDay()])
                ->orderBy('entry_at')
                ->get();

            $results->transform(function ($entry) { // Limpeza
                $entry->entry_reason = Str::limit(preg_replace('/[\r\n]+/', ' ', $entry->entry_reason), 80);
                return $entry;
            });
        } elseif ($reportType === 'official') {
            $results = OfficialTrip::with(['vehicle', 'driver', 'guardDeparture', 'guardArrival'])
                ->where('guard_on_departure_id', $porteiroId) // Ou guard_on_arrival_id
                ->whereBetween('departure_datetime', [$startDate->startOfDay(), $endDate->endOfDay()])
                ->orderBy('departure_datetime')
                ->get();

            $results->transform(function ($trip) { // Limpeza
                $trip->destination = Str::limit(preg_replace('/[\r\n]+/', ' ', $trip->destination), 80);
                $trip->passengers = Str::limit(preg_replace('/[\r\n]+/', ' ', $trip->passengers), 80);
                $trip->return_observation = Str::limit(preg_replace('/[\r\n]+/', ' ', $trip->return_observation), 100);
                return $trip;
            });
        }

        $title = $reportType === 'private' ? 'RELATÓRIO PESSOAL - VEÍCULOS PARTICULARES' : 'RELATÓRIO PESSOAL - FROTA OFICIAL';

        $data = [
            'porteiroName' => $porteiroName,
            'period' => $startDate->format('d/m/Y') . ' a ' . $endDate->format('d/m/Y'),
            'reportType' => $reportType,
            'results' => $results, // Dados limpos
            'title' => $title,
        ];

        $pdf = Pdf::loadView('reports.pdf.personal', $data);
        $pdf->setPaper('a4', 'landscape');
        $fileName = 'relatorio_pessoal_' . Str::slug($porteiroName) . '_' . $startDate->format('Ym') . '.pdf';

        return $pdf->stream($fileName);
    }

    /**
     * Gera o relatório para um único veículo oficial (usando o template pessoal).
     * Nota: A lógica de mapeamento pode precisar de ajuste dependendo do template 'personal'.
     */
    public function generateOfficialVehiclePDF(Request $request)
    {
        $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after_or_equal:start_date',
        ]);

        $startDate = Carbon::parse($request->start_date);
        $endDate   = Carbon::parse($request->end_date);
        $vehicle = Vehicle::withTrashed()->findOrFail($request->vehicle_id);

        $trips = OfficialTrip::with(['driver', 'guardDeparture', 'guardArrival'])
            ->where('vehicle_id', $request->vehicle_id)
            ->whereBetween('departure_datetime', [$startDate->startOfDay(), $endDate->endOfDay()])
            ->orderBy('departure_datetime', 'asc')
            ->get();

        // Limpeza e Mapeamento
        $results = $trips->map(function ($trip) {
            // Limpa dados ANTES de mapear
            $trip->destination = Str::limit(preg_replace('/[\r\n]+/', ' ', $trip->destination), 80);
            if ($trip->driver) {
                $trip->driver->name = preg_replace('/[\r\n]+/', ' ', $trip->driver->name);
            }
            // ... limpar outros ...

            // Mapeia
            return (object) [
                'driver' => $trip->driver,
                'vehicle' => $trip,
                'entry_at' => $trip->departure_datetime,
                'exit_at' => $trip->arrival_datetime,
                'entry_reason' => $trip->destination,
                'guardEntry' => $trip->guardDeparture,
                'guardExit' => $trip->guardArrival,
                'license_plate' => $trip->vehicle?->license_plate,
                'vehicle_model' => $trip->vehicle?->model,
            ];
        });

        $data = [
            'title'            => 'Relatório de Utilização de Veículo Oficial',
            'reportType'       => 'vehicle',
            'vehicleDescription' => "{$vehicle->model} ({$vehicle->license_plate})",
            'period'           => $startDate->format('d/m/Y') . ' a ' . $endDate->format('d/m/Y'),
            'results'          => $results,
        ];

        $pdf = Pdf::loadView('reports.pdf.personal', $data);
        $pdf->setPaper('a4', 'portrait');
        $fileName = 'relatorio_veiculo_' . Str::slug($vehicle->license_plate) . '_' . $startDate->format('Ym') . '.pdf';

        return $pdf->stream($fileName);
    }

    /**
     * Processa a submissão de relatório via formulário (NÃO Livewire).
     * Este método parece redundante se a submissão for feita pelo GuardReport.php (Livewire).
     * Se ainda for usado, PRECISA SER CORRIGIDO.
     */
    public function submitGuardReport(Request $request)
    {
        // !! ATENÇÃO: Este método ainda usa nomes. Se ele for realmente utilizado,
        // !! precisa ser corrigido como fizemos nos outros locais.
        // !! Se a submissão é feita APENAS pelo componente Livewire GuardReport,
        // !! este método pode ser REMOVIDO.

        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'submission_type' => 'required|string|in:private,official',
            'vehicle_id' => 'nullable|required_if:submission_type,official|exists:vehicles,id', // Obrigatório se oficial
        ]);

        $guardId = Auth::id(); // <-- USA O ID
        // $guardName = Auth::user()->name; // Não usar mais para filtrar
        $start = Carbon::parse($request->start_date)->startOfDay();
        $end = Carbon::parse($request->end_date)->endOfDay();

        // Verifica se já existe submissão para este porteiro/período/tipo (e veículo se oficial)
        $existingQuery = ReportSubmission::where('guard_id', $guardId)
            ->whereYear('start_date', $start->year)
            ->whereMonth('start_date', $start->month)
            ->where('type', $request->submission_type);

        if ($request->submission_type === 'official') {
            $existingQuery->where('vehicle_id', $request->vehicle_id);
        }

        if ($existingQuery->exists()) {
            return back()->with('error', 'Você já submeteu um relatório deste tipo para este período.');
        }


        // Atribuição de Fiscal (Lógica mantida, mas pode ser melhorada)
        $fiscalType = $request->submission_type === 'private' ? ['private', 'both'] : ['official', 'both'];
        $fiscal = User::where('role', 'fiscal')->whereIn('fiscal_type', $fiscalType)->inRandomOrder()->first();


        $submissionData = [
            'guard_id' => $guardId,
            'assigned_fiscal_id' => $fiscal->id ?? null, // Atribui fiscal aleatório
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'submitted_at' => now(),
            'status' => 'pending',
            'type' => $request->submission_type, // Adiciona o tipo
        ];

        if ($request->submission_type === 'private') {
            // ### CORREÇÃO AQUI ###
            // Filtra pelos registros que o porteiro logado finalizou (registrou a saída)
            $entriesToSubmit = PrivateEntry::where('guard_on_exit_id', $guardId) // <-- CORRIGIDO para ID
                ->whereBetween('entry_at', [$start, $end]) // Idealmente filtrar por exit_at, mas entry_at funciona se exit_at não for null
                ->whereNotNull('exit_at') // Garante que foi finalizado
                ->whereNull('report_submission_id')->get();

            if ($entriesToSubmit->isEmpty()) {
                return back()->with('error', 'Não há registos de veículos particulares finalizados por você para submeter neste período.');
            }

            $submission = ReportSubmission::create($submissionData);
            PrivateEntry::whereIn('id', $entriesToSubmit->pluck('id'))->update(['report_submission_id' => $submission->id]);

            return back()->with('message', 'Relatório de veículos particulares submetido com sucesso.');
        } elseif ($request->submission_type === 'official') {

            // Filtra pelas viagens que o porteiro logado finalizou (registrou a chegada)
            $tripsToSubmit = OfficialTrip::where('guard_on_arrival_id', $guardId)
                ->where('vehicle_id', $request->vehicle_id)
                ->whereBetween('departure_datetime', [$start, $end]) // Filtra pelo período da viagem
                ->whereNotNull('arrival_datetime') // Garante que foi finalizado
                ->whereNull('report_submission_id')->get();

            if ($tripsToSubmit->isEmpty()) {
                return back()->with('error', 'Não há viagens finalizadas por você para este veículo no período selecionado.');
            }

            $submissionData['vehicle_id'] = $request->vehicle_id;
            // Adicionar observação se houver campo no formulário:
            $submissionData['observation'] = $request->input('observation', null);
            $submission = ReportSubmission::create($submissionData);

            OfficialTrip::whereIn('id', $tripsToSubmit->pluck('id'))->update(['report_submission_id' => $submission->id]);

            $vehicle = Vehicle::find($request->vehicle_id); // Usar find para evitar erro se não encontrar
            return back()->with('message', "Relatório para o veículo {$vehicle?->model} ({$vehicle?->license_plate}) foi submetido com sucesso.");
        }

        return back()->with('error', 'Tipo de submissão inválido.');
    }
}
