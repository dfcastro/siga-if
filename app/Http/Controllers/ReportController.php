<?php

namespace App\Http\Controllers;

use App\Models\OfficialTrip;
use App\Models\PrivateEntry;
use App\Models\Vehicle;
use App\Models\Driver;
use App\Models\ReportSubmission;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ReportController extends Controller
{
    /**
     * Gera PDF de Auditoria para a Frota Oficial (Geral)
     */
    public function officialVehiclesPDF(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'vehicle_id' => 'required|exists:vehicles,id',
            'driver_id' => 'nullable|exists:drivers,id',
        ]);

        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);
        $user = Auth::user();

        $query = OfficialTrip::with([
            'driver' => fn($q) => $q->withTrashed(),
            'vehicle' => fn($q) => $q->withTrashed(),
            'guardDeparture',
            'guardArrival'
        ])
            ->where('vehicle_id', $request->vehicle_id)
            ->whereBetween('departure_datetime', [$startDate->startOfDay(), $endDate->endOfDay()])
            ->whereNotNull('arrival_datetime');

        if ($request->filled('driver_id')) {
            $query->where('driver_id', $request->driver_id);
        }

        // BARRICADA INVISÍVEL DE SEGURANÇA: Se for porteiro, filtra apenas os registros finalizados por ELE.
        if ($user->role === 'porteiro') {
            $query->where('guard_on_arrival_id', $user->id);
        }

        $trips = $query->orderBy('departure_datetime')->get();

        $trips->transform(function ($trip) {
            // Proteção (?? '') contra valores nulos no preg_replace (Evita erros fatais no PHP 8+)
            $trip->destination = Str::limit(preg_replace('/[\r\n]+/', ' ', $trip->destination ?? ''), 80);
            $trip->passengers = Str::limit(preg_replace('/[\r\n]+/', ' ', $trip->passengers ?? ''), 80);
            $trip->return_observation = Str::limit(preg_replace('/[\r\n]+/', ' ', $trip->return_observation ?? ''), 100);

            if ($trip->driver) $trip->driver->name = preg_replace('/[\r\n]+/', ' ', $trip->driver->name ?? '');
            if ($trip->vehicle) $trip->vehicle->model = preg_replace('/[\r\n]+/', ' ', $trip->vehicle->model ?? '');
            if ($trip->guardDeparture) $trip->guardDeparture->name = preg_replace('/[\r\n]+/', ' ', $trip->guardDeparture->name ?? '');
            if ($trip->guardArrival) $trip->guardArrival->name = preg_replace('/[\r\n]+/', ' ', $trip->guardArrival->name ?? '');

            return $trip;
        });

        $vehicle = Vehicle::withTrashed()->find($request->vehicle_id);
        $driver = $request->filled('driver_id') ? Driver::withTrashed()->find($request->driver_id) : null;
        $totalKm = $trips->sum('distance_traveled');

        $data = [
            'vehicle' => $vehicle,
            'driver' => $driver,
            'trips' => $trips,
            'startDate' => $startDate->format('d/m/Y'),
            'endDate' => $endDate->format('d/m/Y'),
            'totalKm' => $totalKm,
            'generatorName' => $user->name,
            'generatorRole' => $user->role, // Adicionado para auditoria no PDF
        ];

        $pdf = Pdf::loadView('reports.pdf.official', $data);
        $pdf->setPaper('a4', 'landscape');
        $fileName = 'extrato_oficial_' . Str::slug($vehicle->license_plate ?? 'veiculo') . '_' . $startDate->format('Ym') . '.pdf';

        return $pdf->stream($fileName);
    }

    /**
     * Gera PDF de Auditoria para Veículos Particulares (Geral)
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
        $user = Auth::user();

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

        // BARRICADA INVISÍVEL DE SEGURANÇA: Se for porteiro, filtra apenas os registros finalizados por ELE.
        if ($user->role === 'porteiro') {
            $query->where('guard_on_exit_id', $user->id);
        }

        $entries = $query->orderBy('entry_at')->get();

        $entries->transform(function ($entry) {
            // Proteção (?? '') contra valores nulos
            $entry->entry_reason = Str::limit(preg_replace('/[\r\n]+/', ' ', $entry->entry_reason ?? ''), 80);
            if ($entry->driver) $entry->driver->name = preg_replace('/[\r\n]+/', ' ', $entry->driver->name ?? '');
            if ($entry->guardExit) $entry->guardExit->name = preg_replace('/[\r\n]+/', ' ', $entry->guardExit->name ?? '');
            if ($entry->guardEntry) $entry->guardEntry->name = preg_replace('/[\r\n]+/', ' ', $entry->guardEntry->name ?? '');
            return $entry;
        });

        $vehicle = $request->filled('vehicle_id') ? Vehicle::withTrashed()->find($request->vehicle_id) : null;
        $driver = $request->filled('driver_id') ? Driver::withTrashed()->find($request->driver_id) : null;

        $data = [
            'entries' => $entries,
            'startDate' => $startDate->format('d/m/Y'),
            'endDate' => $endDate->format('d/m/Y'),
            'vehicle' => $vehicle,
            'driver' => $driver,
            'generatorName' => $user->name,
            'generatorRole' => $user->role, // Adicionado para auditoria no PDF
        ];

        $pdf = Pdf::loadView('reports.pdf.private', $data);
        $pdf->setPaper('a4', 'landscape');
        $fileName = 'extrato_particulares_' . $startDate->format('Ym') . '_' . now()->format('His') . '.pdf';

        return $pdf->stream($fileName);
    }
}
