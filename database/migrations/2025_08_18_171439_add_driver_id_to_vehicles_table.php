<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            // 1. Apagamos a coluna antiga que não será mais usada
            $table->dropColumn('owner_name');

            // 2. Adicionamos a nova coluna para o ID do motorista
            // onDelete('cascade') significa que se um motorista for excluído, todos os veículos associados a ele também serão.
            $table->foreignId('driver_id')
                  ->constrained('drivers')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            // Para reverter, apagamos a chave estrangeira e a coluna
            $table->dropForeign(['driver_id']);
            $table->dropColumn('driver_id');

            // E adicionamos a coluna antiga de volta
            $table->string('owner_name');
        });
    }
};