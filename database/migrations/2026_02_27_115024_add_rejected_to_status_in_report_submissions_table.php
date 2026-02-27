<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Altera a coluna status para incluir a opção 'rejected'
        DB::statement("ALTER TABLE report_submissions MODIFY COLUMN status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverte a coluna para o estado original
        DB::statement("ALTER TABLE report_submissions MODIFY COLUMN status ENUM('pending', 'approved') DEFAULT 'pending'");
    }
};

