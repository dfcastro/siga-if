<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\CreatePrivateEntry; // Importamos nosso componente aqui

// Quando alguém acessar a página inicial '/', carregue o componente CreatePrivateEntry
Route::get('/', CreatePrivateEntry::class);



?>