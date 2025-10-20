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
            // Adicione esta linha
            $table->text('observation')->nullable()->after('end_date'); // O after() é opcional, mas ajuda a organizar a tabela
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('report_submissions', function (Blueprint $table) {
            // Opcional, mas boa prática: adicione o código para remover a coluna
            $table->dropColumn('observation');
        });
    }
};