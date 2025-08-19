<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\CreatePrivateEntry;
use App\Livewire\DriverManagement;
use App\Livewire\VehicleManagement; // Adicione esta linha

Route::get('/', CreatePrivateEntry::class);
Route::get('/motoristas', DriverManagement::class);

Route::get('/veiculos', VehicleManagement::class); // Adicione esta nova rota