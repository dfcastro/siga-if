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
        // Modifica a tabela official_trips
        Schema::table('official_trips', function (Blueprint $table) {
            // Adiciona colunas para IDs dos porteiros (nullable inicialmente)
            $table->foreignId('guard_on_departure_id')->nullable()->after('guard_on_departure')->constrained('users')->onDelete('set null');
            $table->foreignId('guard_on_arrival_id')->nullable()->after('guard_on_arrival')->constrained('users')->onDelete('set null');
        });

        // Modifica a tabela private_entries
        Schema::table('private_entries', function (Blueprint $table) {
            // Adiciona colunas para IDs dos porteiros (nullable inicialmente)
            $table->foreignId('guard_on_entry_id')->nullable()->after('guard_on_entry')->constrained('users')->onDelete('set null');
            $table->foreignId('guard_on_exit_id')->nullable()->after('guard_on_exit')->constrained('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('official_trips', function (Blueprint $table) {
            // Remove as chaves estrangeiras e as colunas
            $table->dropForeign(['guard_on_departure_id']);
            $table->dropForeign(['guard_on_arrival_id']);
            $table->dropColumn(['guard_on_departure_id', 'guard_on_arrival_id']);
        });

        Schema::table('private_entries', function (Blueprint $table) {
            // Remove as chaves estrangeiras e as colunas
            $table->dropForeign(['guard_on_entry_id']);
            $table->dropForeign(['guard_on_exit_id']);
            $table->dropColumn(['guard_on_entry_id', 'guard_on_exit_id']);
        });
    }
};