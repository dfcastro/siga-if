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
use Illuminate\Support\Facades\Route;
use App\Livewire\GuardReport;
use App\Livewire\FiscalApproval;
use App\Livewire\ReportStatus;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    // Se o usuário já estiver logado, manda direto pro painel!
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }
    // Se não estiver, mostra a tela de login
    return view('auth/login');
});

Route::get('/dashboard', DashboardController::class)
    ->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {

    // --- PÁGINAS DE GESTÃO ---
    Route::get('/users', UserManagement::class)->name('users.index')->middleware('role:admin');
    Route::get('/vehicles', VehicleManagement::class)->name('vehicles.index')->middleware('role:admin,porteiro,fiscal');
    Route::get('/drivers', DriverManagement::class)->name('drivers.index')->middleware('role:admin,porteiro,fiscal');

    // Pesquisa Avançada e Extratos (Com barreira invisível para o porteiro)
    Route::get('/reports', Reports::class)->name('reports')->middleware('role:admin,fiscal,porteiro');

    // --- PÁGINAS DE OPERAÇÃO E PORTARIA ---
    Route::get('/entries/private/create', CreatePrivateEntry::class)->name('entries.create');
    Route::get('/fleet', OfficialFleetManagement::class)->name('fleet.index')->middleware('role:porteiro,admin');

    // --- ROTAS DE GERAÇÃO DE PDF (Auditoria) ---
    Route::get('/reports/official/pdf', [ReportController::class, 'officialVehiclesPDF'])->name('reports.official.pdf');
    Route::get('/reports/private/pdf', [ReportController::class, 'privateVehiclesPDF'])->name('reports.private.pdf');

    // --- FLUXO DE FECHAMENTO MENSAL (Compliance) ---
    // Tela onde o porteiro submete o fechamento do mês
    Route::get('/meus-relatorios', GuardReport::class)->name('guard.report');

    // Tela onde o fiscal aprova/dá visto
    Route::get('/aprovacao-relatorios', FiscalApproval::class)->middleware('role:admin,fiscal')->name('fiscal.approval');

    // Status dos relatórios
    Route::get('/relatorios/status', ReportStatus::class)->name('reports.status');

    // --- ROTAS DE PERFIL ---
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
