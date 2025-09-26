<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PrivateEntry;
use App\Models\OfficialTrip;

class DashboardController extends Controller
{
    public function index()
    {
        $privateVehiclesIn = PrivateEntry::whereNull('exit_at')->count();

        // CORREÇÃO: Usar a coluna 'arrival_datetime' para frota oficial
        $officialTripsOngoing = OfficialTrip::whereNull('arrival_datetime')->count();

        return view('dashboard', [
            'privateVehiclesIn' => $privateVehiclesIn,
            'officialTripsOngoing' => $officialTripsOngoing,
        ]);
    }
}
