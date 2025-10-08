<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            // Torna a coluna driver_id opcional (pode ser nula)
            $table->foreignId('driver_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            // Reverte a coluna para não-nula, se necessário
            $table->foreignId('driver_id')->nullable(false)->change();
        });
    }
};