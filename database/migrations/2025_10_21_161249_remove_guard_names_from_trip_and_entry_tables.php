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
        Schema::table('official_trips', function (Blueprint $table) {
            $table->dropColumn(['guard_on_departure', 'guard_on_arrival']);
        });

         Schema::table('private_entries', function (Blueprint $table) {
            $table->dropColumn(['guard_on_entry', 'guard_on_exit']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
         Schema::table('official_trips', function (Blueprint $table) {
            $table->string('guard_on_departure')->nullable()->after('departure_datetime');
            $table->string('guard_on_arrival')->nullable()->after('arrival_datetime');
            // Nota: Não podemos recriar os dados aqui facilmente.
            // Se precisar reverter, terá de restaurar um backup ou criar outro backfill.
        });

        Schema::table('private_entries', function (Blueprint $table) {
            $table->string('guard_on_entry')->nullable()->after('entry_at');
            $table->string('guard_on_exit')->nullable()->after('exit_at');
            // Nota: Mesma limitação do 'down' acima.
        });
    }
};