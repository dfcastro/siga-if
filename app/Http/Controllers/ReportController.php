<?php

namespace App\Http\Controllers;

use App\Models\OfficialTrip;
use App\Models\PrivateEntry;
use App\Models\Vehicle;
use App\Models\Driver;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\ReportSubmission;


class ReportController extends Controller
{
    public function officialVehiclesPDF(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'vehicle_id' => 'nullable|exists:vehicles,id',
            'driver_id' => 'nullable|exists:drivers,id',
        ]);

        $startDate = $request->start_date;
        $endDate = $request->end_date;

        $query = OfficialTrip::with(['driver', 'vehicle' => fn($q) => $q->withTrashed()])
            ->whereBetween('departure_datetime', [$request->start_date, Carbon::parse($request->end_date)->endOfDay()])
            ->whereNotNull('arrival_datetime');

        if ($request->filled('vehicle_id')) {
            $query->where('vehicle_id', $request->vehicle_id);
        }

        if ($request->filled('driver_id')) {
            $query->where('driver_id', $request->driver_id);
        }

        $trips = $query->orderBy('departure_datetime')->get();

        // Dados para o cabeçalho do PDF
        $vehicle = $request->filled('vehicle_id') ? Vehicle::find($request->vehicle_id) : null;
        $driver = $request->filled('driver_id') ? Driver::find($request->driver_id) : null;

        $totalKm = $trips->sum(fn($trip) => $trip->arrival_odometer - $trip->departure_odometer);

        // Prepara os dados para a view do PDF
        $data = [
            'vehicle' => $vehicle,
            'driver' => $driver,
            'trips' => $trips,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'totalKm' => $totalKm,
        ];

        $pdf = Pdf::loadView('reports.pdf.official', $data);
        $pdf->setPaper('a4', 'landscape');
        $fileName = 'relatorio_oficial_' . now()->format('Y-m-d') . '.pdf';

        return $pdf->stream($fileName);
    }

    public function privateVehiclesPDF(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'vehicle_id' => 'nullable|exists:vehicles,id',
            'driver_id' => 'nullable|exists:drivers,id',
        ]);

        $startDate = $request->start_date;
        $endDate = $request->end_date;

        $query = PrivateEntry::with(['vehicle', 'driver'])
            ->whereBetween('entry_at', [$startDate, Carbon::parse($endDate)->endOfDay()])
            ->whereNotNull('exit_at');

        if ($request->filled('vehicle_id')) {
            $query->where('vehicle_id', $request->vehicle_id);
        }

        if ($request->filled('driver_id')) {
            $query->where('driver_id', $request->driver_id);
        }

        $entries = $query->orderBy('entry_at')->get();

        // Dados para o cabeçalho do PDF
        $vehicle = $request->filled('vehicle_id') ? Vehicle::find($request->vehicle_id) : null;
        $driver = $request->filled('driver_id') ? Driver::find($request->driver_id) : null;

        // **A CORREÇÃO ESTÁ AQUI**
        // Agora passamos todas as variáveis que a view pode precisar.
        $data = [
            'entries' => $entries,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'vehicle' => $vehicle, // O veículo específico filtrado (pode ser null)
            'driver' => $driver,   // O motorista específico filtrado (pode ser null)
            'vehicles' => Vehicle::orderBy('model')->get(), // A lista completa de veículos
            'drivers' => Driver::orderBy('name')->get(),     // A lista completa de motoristas
        ];

        $pdf = Pdf::loadView('reports.pdf.private', $data);
        $pdf->setPaper('a4', 'landscape');
        $fileName = 'relatorio_particulares_' . now()->format('Y-m-d') . '.pdf';

        return $pdf->stream($fileName);
    }

    // MÉTODO PARA O RELATÓRIO PESSOAL DO PORTEIRO
    public function generatePersonalPDF(Request $request)
    {
        $request->validate([
            'type' => 'required|in:private,official',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $porteiroName = Auth::user()->name;
        $reportType = $request->type;
        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);

        $results = collect(); // Inicia uma coleção vazia

        if ($reportType === 'private') {
            $results = PrivateEntry::with(['vehicle', 'driver'])
                ->where('guard_on_entry', $porteiroName)
                ->whereBetween('entry_at', [$startDate->startOfDay(), $endDate->endOfDay()])
                ->orderBy('entry_at')
                ->get();
        } elseif ($reportType === 'official') {
            $results = OfficialTrip::with(['vehicle', 'driver'])
                ->where('guard_on_departure', $porteiroName)
                ->whereBetween('departure_datetime', [$startDate->startOfDay(), $endDate->endOfDay()])
                ->orderBy('departure_datetime')
                ->get();
        }

        $title = $reportType === 'private'
            ? 'RELATÓRIO PESSOAL - VEÍCULOS PARTICULARES'
            : 'RELATÓRIO PESSOAL - FROTA OFICIAL';

        $data = [
            'porteiroName' => $porteiroName,
            'period' => $startDate->format('d/m/Y') . ' a ' . $endDate->format('d/m/Y'),
            'reportType' => $reportType,
            'results' => $results,
            'title' => $title, // Passa o título para a view
        ];

        $pdf = Pdf::loadView('reports.pdf.personal', $data);
        $pdf->setPaper('a4', 'landscape');
        $fileName = 'relatorio_pessoal_' . Str::slug($porteiroName) . '_' . now()->format('Y-m-d') . '.pdf';

        return $pdf->stream($fileName);
    }

    // GERA O RELATÓRIO PARA UM ÚNICO VEÍCULO OFICIAL
    public function generateOfficialVehiclePDF(Request $request)
    {
        // Valida se recebemos os dados necessários
        $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id', // O ID do veículo é obrigatório
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after_or_equal:start_date',
        ]);

        $vehicleId = $request->vehicle_id;
        $startDate = Carbon::parse($request->start_date);
        $endDate   = Carbon::parse($request->end_date);

        // Busca os dados do veículo específico
        $vehicle = Vehicle::withTrashed()->findOrFail($request->vehicle_id);

        // Busca as viagens APENAS para este veículo e período
        $trips = OfficialTrip::with(['driver'])
            ->where('vehicle_id', $request->vehicle_id)
            ->whereBetween('departure_datetime', [Carbon::parse($request->start_date)->startOfDay(), Carbon::parse($request->end_date)->endOfDay()])
            ->orderBy('departure_datetime', 'asc')
            ->get();
            
        // Renomeia a variável de resultados para 'results' para ser compatível com o template
        // E mapeia os campos para os nomes que o template espera (entry_at, exit_at, etc.)
        $results = $trips->map(function ($trip) {
            return (object) [
                'driver'         => $trip->driver,
                'entry_at'       => $trip->departure_datetime,
                'exit_at'        => $trip->arrival_datetime,
                'entry_reason'   => $trip->destination, // Usamos o destino como motivo
                'guard_on_exit'  => null, // Não aplicável neste relatório
            ];
        });

        // Prepara as variáveis que o nosso template flexível precisa
        $data = [
            'title'              => 'Relatório de Utilização de Veículo Oficial',
            'reportType'         => 'vehicle', // Informa ao template qual layout usar
            'vehicleDescription' => "{$vehicle->model} ({$vehicle->year}) - Placa: {$vehicle->license_plate}",
            'period'             => $startDate->format('d/m/Y') . ' a ' . $endDate->format('d/m/Y'),
            'results'            => $results,
        ];

        // Usa o nosso template 'personal.blade.php' que já está pronto
        $pdf = Pdf::loadView('reports.pdf.personal', $data);
        $pdf->setPaper('a4', 'portrait'); // Relatório por veículo fica melhor em modo retrato

        $fileName = 'relatorio_veiculo_' . Str::slug($vehicle->license_plate) . '_' . now()->format('Y-m-d') . '.pdf';

        return $pdf->stream($fileName);
    }
    public function submitGuardReport(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'submission_type' => 'required|string|in:private,official',
            'vehicle_id' => 'nullable|exists:vehicles,id', // Para submissões de veículos oficiais
        ]);

        $guardName = Auth::user()->name;
        $start = Carbon::parse($request->start_date)->startOfDay();
        $end = Carbon::parse($request->end_date)->endOfDay();

        $submissionData = [
            'guard_id' => Auth::id(),
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'submitted_at' => now(),
            'status' => 'pending',
        ];

        if ($request->submission_type === 'private') {
            $entriesToSubmit = PrivateEntry::where('guard_on_entry', $guardName)
                ->whereBetween('entry_at', [$start, $end])
                ->whereNull('report_submission_id')->get();

            if ($entriesToSubmit->isEmpty()) {
                return back()->with('error', 'Não há registos de veículos particulares para submeter neste período.');
            }

            $submission = ReportSubmission::create($submissionData);
            PrivateEntry::whereIn('id', $entriesToSubmit->pluck('id'))->update(['report_submission_id' => $submission->id]);

            return back()->with('message', 'Relatório de veículos particulares submetido com sucesso.');
        } elseif ($request->submission_type === 'official') {

            $tripsToSubmit = OfficialTrip::where('guard_on_departure', $guardName)
                ->where('vehicle_id', $request->vehicle_id)
                ->whereBetween('departure_datetime', [$start, $end])
                ->whereNull('report_submission_id')->get();

            if ($tripsToSubmit->isEmpty()) {
                return back()->with('error', 'Não há viagens para este veículo no período selecionado.');
            }

            $submissionData['vehicle_id'] = $request->vehicle_id;
            $submission = ReportSubmission::create($submissionData);

            OfficialTrip::whereIn('id', $tripsToSubmit->pluck('id'))->update(['report_submission_id' => $submission->id]);

            $vehicle = Vehicle::find($request->vehicle_id);
            return back()->with('message', "Relatório para o veículo {$vehicle->model} ({$vehicle->license_plate}) foi submetido com sucesso.");
        }

        return back()->with('error', 'Tipo de submissão inválido.');
    }
}
