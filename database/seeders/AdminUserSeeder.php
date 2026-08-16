<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@ifnmg.edu.br'], // Condição de busca (evita duplicatas)
            [
                'name' => 'Administrador',
                'password' => 'Qyb58KWz', // O model já faz o cast para 'hashed' automaticamente
                'role' => 'admin', // Atribui a permissão de administrador
            ]
        );
    }
}