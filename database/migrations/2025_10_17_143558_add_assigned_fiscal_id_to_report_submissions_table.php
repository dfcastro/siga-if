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
    Schema::table('report_submissions', function (Blueprint $table) {
        // Guarda o ID do fiscal responsável pela aprovação
        $table->foreignId('assigned_fiscal_id')->nullable()->constrained('users')->after('user_id');
    });
}

public function down(): void
{
    Schema::table('report_submissions', function (Blueprint $table) {
        $table->dropForeign(['assigned_fiscal_id']);
        $table->dropColumn('assigned_fiscal_id');
    });
}
};
