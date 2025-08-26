<?php

namespace App\Http\Controllers;

use App\Models\OfficialTrip;
use App\Models\PrivateEntry;
use App\Models\Vehicle;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function officialVehiclesPDF(Request $request)
    {
        $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
        ]);

        $vehicle = Vehicle::findOrFail($request->vehicle_id);
        $startDate = $request->start_date;
        $endDate = $request->end_date;

        $trips = OfficialTrip::with('driver')
            ->where('vehicle_id', $vehicle->id)
            ->whereBetween('departure_datetime', [$startDate, $endDate])
            ->whereNotNull('arrival_datetime')
            ->orderBy('departure_datetime')
            ->get();

        $totalKm = $trips->sum(function ($trip) {
            return $trip->arrival_odometer - $trip->departure_odometer;
        });

        $pdf = Pdf::loadView('reports.pdf.official', compact('vehicle', 'trips', 'startDate', 'totalKm'));
        $pdf->setPaper('a4', 'landscape');
        // Gera um nome de arquivo dinâmico
        $fileName = 'relatorio_oficial_' . $vehicle->license_plate . '_' . Carbon::parse($startDate)->format('m-Y') . '.pdf';

        return $pdf->stream($fileName);
    }

    public function privateVehiclesPDF(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date',
        ]);

        $startDate = $request->start_date;
        $endDate = $request->end_date;

        $entries = PrivateEntry::with(['vehicle', 'driver'])
            ->whereBetween('entry_at', [$startDate, $endDate])
            ->whereNotNull('exit_at')
            ->orderBy('entry_at')
            ->get();

        $pdf = Pdf::loadView('reports.pdf.private', compact('entries', 'startDate'));
        $pdf->setPaper('a4', 'landscape');
        // Gera um nome de arquivo dinâmico
        $fileName = 'relatorio_particulares_' . Carbon::parse($startDate)->format('m-Y') . '.pdf';

        return $pdf->stream($fileName);
    }
}
