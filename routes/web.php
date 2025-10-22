<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ReportController;
use App\Livewire\CreatePrivateEntry;
use App\Livewire\DriverManagement;
use App\Livewire\OfficialFleetManagement;
use App\Livewire\UserManagement;
use App\Livewire\VehicleManagement;
use App\Livewire\Reports;
use App\Livewire\PersonalReport;
use Illuminate\Support\Facades\Route;
use App\Livewire\GuardReport;
use App\Livewire\FiscalApproval;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('auth/login');
});

// --- CORREÇÃO APLICADA AQUI ---
// A rota agora aponta para a classe do controlador, sem especificar o método 'index'.
Route::get('/dashboard', DashboardController::class)
    ->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {

    // --- PÁGINAS DE GESTÃO ---
    Route::get('/users', UserManagement::class)->name('users.index')->middleware('role:admin');
    Route::get('/vehicles', VehicleManagement::class)->name('vehicles.index')->middleware('role:admin,porteiro,fiscal');
    Route::get('/drivers', DriverManagement::class)->name('drivers.index')->middleware('role:admin,porteiro,fiscal');
    Route::get('/reports', Reports::class)->name('reports')->middleware('role:admin,fiscal,porteiro');

    // ROTA PARA A PÁGINA DE RELATÓRIO PESSOAL DO PORTEIRO
    // Route::get('/meu-relatorio', PersonalReport::class)
    //     ->name('reports')
    //     ->middleware('role:porteiro');

    // --- PÁGINAS DE OPERAÇÃO ---
    Route::get('/entries/private/create', CreatePrivateEntry::class)->name('entries.create');
    Route::get('/fleet', OfficialFleetManagement::class)->name('fleet.index')->middleware('role:porteiro,admin');

    // --- ROTAS DE GERAÇÃO DE PDF ---
    Route::get('/reports/official/pdf', [ReportController::class, 'officialVehiclesPDF'])->name('reports.official.pdf');
    Route::get('/reports/private/pdf', [ReportController::class, 'privateVehiclesPDF'])->name('reports.private.pdf');
    Route::get('/reports/official-vehicle-pdf', [ReportController::class, 'generateOfficialVehiclePDF'])->name('reports.officialVehicle.pdf');

    // Route::get('/meu-relatorio/pdf', [ReportController::class, 'generatePersonalPDF'])
    //     ->name('reports.personal.pdf')
    //     ->middleware('role:porteiro');

    Route::get('/meus-relatorios', GuardReport::class)->name('guard.report');

    // Rota para a aprovação do Fiscal
    Route::get('/aprovacao-relatorios', FiscalApproval::class)
        ->middleware('role:admin,fiscal')
        ->name('fiscal.approval');

    // Rota para processar a submissão do relatório do porteiro
    Route::post('/reports/submit-guard-report', [ReportController::class, 'submitGuardReport'])->name('reports.submitGuardReport');

    Route::get('/relatorios/status', \App\Livewire\ReportStatus::class)
        ->name('reports.status');

    // --- ROTAS DE PERFIL ---
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
