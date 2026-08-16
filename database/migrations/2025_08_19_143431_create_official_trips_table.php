<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('official_trips', function (Blueprint $table) {
            $table->id();

            $table->foreignId('vehicle_id')->constrained('vehicles');
            $table->foreignId('driver_id')->constrained('drivers');

            $table->string('destination');
            $table->text('passengers')->nullable();

            $table->dateTime('departure_datetime');
            $table->unsignedInteger('departure_odometer');

            $table->dateTime('arrival_datetime')->nullable(); // Nulo até o retorno
            $table->unsignedInteger('arrival_odometer')->nullable(); // Nulo até o retorno

            $table->string('guard_on_departure');
            $table->string('guard_on_arrival')->nullable();

            $table->text('notes')->nullable(); // Um campo extra para observações

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('official_trips');
    }
};