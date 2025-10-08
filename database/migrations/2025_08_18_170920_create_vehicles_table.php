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
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->string('license_plate', 10)->unique(); // Placa do veículo, ex: 'ABC-1234'. unique() para não ter placas repetidas
            $table->string('model'); // Modelo, ex: 'Fiat Uno'
            $table->string('color'); // Cor, ex: 'Branco'
            $table->string('owner_name'); // Nome do proprietário
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
