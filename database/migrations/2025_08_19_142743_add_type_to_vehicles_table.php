<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            // Adiciona a nova coluna após a coluna 'owner_name'
            $table->string('type')->default('Particular');
        });
    }

    public function down(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            // Remove a coluna caso seja necessário reverter
            $table->dropColumn('type');
        });
    }
};