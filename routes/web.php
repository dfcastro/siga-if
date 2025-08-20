<?php

use App\Http\Controllers\ProfileController; // Necessário para as rotas de perfil
use App\Livewire\CreatePrivateEntry;
use App\Livewire\DriverManagement;
use App\Livewire\OfficialFleetManagement;
use App\Livewire\UserManagement;
use App\Livewire\VehicleManagement;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Rota principal: redireciona para o login
Route::get('/', function () {
    return redirect()->route('login');
});

// Agrupamos todas as rotas do nosso sistema que precisam de autenticação
Route::middleware(['auth', 'verified'])->group(function () {

    // A tela principal de registro de entrada/saída agora é o nosso "Dashboard"
    Route::get('/dashboard', CreatePrivateEntry::class)->name('dashboard');

    // Nossas outras páginas de gerenciamento
    Route::get('/motoristas', DriverManagement::class)->name('drivers.index');
    Route::get('/veiculos', VehicleManagement::class)->name('vehicles.index');
    Route::get('/frota', OfficialFleetManagement::class)->name('fleet.index');

    // Rota de gerenciamento de usuários, protegida para admins
    Route::get('/usuarios', UserManagement::class)->name('users.index')->middleware('is_admin');

    // Rotas de Perfil (adicionadas aqui, como você fez)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Esta linha ainda é necessária para carregar /login, /register, /logout, etc.
require __DIR__ . '/auth.php';