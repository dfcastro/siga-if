<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('official_trips', function (Blueprint $table) {
            // Adiciona um campo de texto para a observação ou previsão de retorno.
            // Será 'nullable' porque é opcional.
            $table->text('return_observation')->nullable()->after('passengers');
        });
    }

    public function down(): void
    {
        Schema::table('official_trips', function (Blueprint $table) {
            $table->dropColumn('return_observation');
        });
    }
};
