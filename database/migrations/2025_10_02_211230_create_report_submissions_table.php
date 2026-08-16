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
    Schema::create('report_submissions', function (Blueprint $table) {
        $table->id();
        $table->foreignId('guard_id')->constrained('users'); // O porteiro que submeteu
        $table->date('start_date');
        $table->date('end_date');
        $table->timestamp('submitted_at'); // Data/hora da submissão
        $table->foreignId('fiscal_id')->nullable()->constrained('users'); // O fiscal que deu o visto
        $table->timestamp('approved_at')->nullable(); // Data/hora do visto
        $table->enum('status', ['pending', 'approved'])->default('pending'); // Estado do relatório
        $table->timestamps();
    });

    // Também precisamos de uma forma de ligar os registos a uma submissão.
    // Adicionamos uma coluna `submission_id` às tabelas existentes.
    Schema::table('private_entries', function (Blueprint $table) {
        $table->foreignId('report_submission_id')->nullable()->constrained('report_submissions');
    });

    Schema::table('official_trips', function (Blueprint $table) {
        $table->foreignId('report_submission_id')->nullable()->constrained('report_submissions');
    });
}
/**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('report_submissions');
    }
};
