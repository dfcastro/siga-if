<?php

use App\Http\Controllers\ProfileController;
use App\Livewire\CreatePrivateEntry;
use App\Livewire\DriverManagement;
use App\Livewire\OfficialFleetManagement;
use App\Livewire\UserManagement;
use App\Livewire\VehicleManagement;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\CheckIsAdmin;
use App\Models\PrivateEntry; 
use App\Models\OfficialTrip; 
use App\Livewire\Reports;
use App\Http\Controllers\ReportController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

// Rota do Dashboard Aprimorada
Route::get('/dashboard', function () {
    // Conta os veículos particulares que entraram e ainda não saíram
    $privateVehiclesIn = PrivateEntry::whereNull('exit_at')->count();

    // Conta as viagens da frota oficial que começaram e ainda não terminaram
    $officialTripsOngoing = OfficialTrip::whereNull('arrival_datetime')->count();

    return view('dashboard', [
        'privateVehiclesIn' => $privateVehiclesIn,
        'officialTripsOngoing' => $officialTripsOngoing,
    ]);
})->middleware(['auth', 'verified'])->name('dashboard');


Route::middleware('auth')->group(function () {
    Route::middleware(CheckIsAdmin::class)->group(function () {
        Route::get('/users', UserManagement::class)->name('users.index');
        Route::get('/vehicles', VehicleManagement::class)->name('vehicles.index');
        Route::get('/drivers', DriverManagement::class)->name('drivers.index');
        Route::get('/reports', Reports::class)->name('reports');
    });
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/entries/private/create', CreatePrivateEntry::class)->name('entries.create');
    Route::get('/fleet', OfficialFleetManagement::class)->name('fleet.index');

   //  ROTAS PARA PDF
    Route::get('/reports/official/pdf', [ReportController::class, 'officialVehiclesPDF'])->name('reports.official.pdf');
    Route::get('/reports/private/pdf', [ReportController::class, 'privateVehiclesPDF'])->name('reports.private.pdf');
});

require __DIR__ . '/auth.php';
