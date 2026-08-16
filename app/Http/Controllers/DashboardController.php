<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// Remova os imports dos Models se não forem mais usados aqui
// use App\Models\PrivateEntry;
// use App\Models\OfficialTrip;

class DashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        // --- REMOVA TODA A LÓGICA DE CONTAGEM DAQUI ---
        // $privateVehiclesIn = PrivateEntry::whereNull('exit_at')->count();
        // $officialTripsOngoing = OfficialTrip::whereNull('arrival_datetime')->count();

        // Apenas retorne a view, sem passar as variáveis
        return view('dashboard'); 
    }
}