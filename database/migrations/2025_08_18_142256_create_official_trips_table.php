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
        Schema::create('official_trips', function (Blueprint $table) {
            $table->id();
            $table->string('vehicle_license_plate', 10); // Placa do veículo oficial
            $table->string('driver_name'); // Nome do condutor
            $table->text('passengers')->nullable(); // Passageiros (pode ser nulo)
            $table->string('destination'); // Destino da viagem
            $table->timestamp('departure_time'); // Data e hora da saída
            $table->unsignedInteger('departure_odometer'); // KM de saída
            $table->timestamp('arrival_time')->nullable(); // Data e hora da chegada
            $table->unsignedInteger('arrival_odometer')->nullable(); // KM de chegada
            $table->string('guard_on_departure'); // Porteiro na saída
            $table->string('guard_on_arrival')->nullable(); // Porteiro na chegada
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('official_trips');
    }
};
