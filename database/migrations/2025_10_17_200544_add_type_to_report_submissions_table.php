<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('report_submissions', function (Blueprint $table) {
            // Adiciona a coluna 'type' para guardar se o relatório é 'official' ou 'private'
            $table->string('type')->after('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('report_submissions', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }

};
