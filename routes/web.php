<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController; // Adicionado
use App\Http\Controllers\ReportController;
use App\Livewire\CreatePrivateEntry;
use App\Livewire\DriverManagement;
use App\Livewire\OfficialFleetManagement;
use App\Livewire\UserManagement;
use App\Livewire\VehicleManagement;
use App\Livewire\Reports;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\CheckIsAdmin;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('auth/login');
});

// Rota do Dashboard agora aponta para o DashboardController
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])->name('dashboard');


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

    // ROTAS PARA PDF
    Route::get('/reports/official/pdf', [ReportController::class, 'officialVehiclesPDF'])->name('reports.official.pdf');
    Route::get('/reports/private/pdf', [ReportController::class, 'privateVehiclesPDF'])->name('reports.private.pdf');
});

require __DIR__ . '/auth.php';
