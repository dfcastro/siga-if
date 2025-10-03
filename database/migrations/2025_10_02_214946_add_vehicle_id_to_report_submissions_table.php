<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // database/migrations/xxxx_add_vehicle_id_to_report_submissions_table.php

    public function up(): void
    {
        Schema::table('report_submissions', function (Blueprint $table) {
            // Esta coluna será preenchida apenas para submissões do tipo "veículo oficial"
            $table->foreignId('vehicle_id')->nullable()->after('guard_id')->constrained('vehicles');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('report_submissions', function (Blueprint $table) {
            //
        });
    }
};
