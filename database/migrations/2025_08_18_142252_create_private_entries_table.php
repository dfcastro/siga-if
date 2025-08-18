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
        Schema::create('private_entries', function (Blueprint $table) {
            $table->id(); // ID único para cada registro
            $table->string('vehicle_model'); // Modelo do veículo (Ex: "Gol", "Onix")
            $table->string('license_plate', 10); // Placa do veículo (Ex: "ABC-1234")
            $table->text('entry_reason'); // Motivo da entrada
            $table->timestamp('entry_at'); // Data e hora da entrada
            $table->timestamp('exit_at')->nullable(); // Data e hora da saída (pode ser nulo)
            $table->string('guard_on_entry'); // Nome do porteiro na entrada
            $table->string('guard_on_exit')->nullable(); // Nome do porteiro na saída
            $table->timestamps(); // Cria as colunas 'created_at' e 'updated_at'
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('private_entries');
    }
};
