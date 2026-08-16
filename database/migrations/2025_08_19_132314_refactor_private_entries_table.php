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
        Schema::table('private_entries', function (Blueprint $table) {
            // Adiciona a coluna para o ID do veículo, que pode ser nula (para visitantes)
            // O 'after' posiciona a coluna depois de 'entry_reason' para organização
            $table->foreignId('vehicle_id')
                  ->nullable()
                  ->after('entry_reason')
                  ->constrained('vehicles')
                  ->onDelete('set null'); // Se o veículo for excluído, o histórico de entrada permanece

            // Adiciona a coluna para o ID do motorista, que também pode ser nula
            $table->foreignId('driver_id')
                  ->nullable()
                  ->after('vehicle_id')
                  ->constrained('drivers')
                  ->onDelete('set null'); // Se o motorista for excluído, o histórico permanece
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('private_entries', function (Blueprint $table) {
            // Remove as colunas e suas restrições na ordem inversa
            $table->dropForeign(['driver_id']);
            $table->dropColumn('driver_id');

            $table->dropForeign(['vehicle_id']);
            $table->dropColumn('vehicle_id');
        });
    }
};