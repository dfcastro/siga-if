<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('official_trips', function (Blueprint $table) {
            $table->unsignedInteger('departure_odometer')->nullable()->after('destination');
            $table->unsignedInteger('arrival_odometer')->nullable()->after('arrival_datetime');
        });
    }

    public function down(): void
    {
        Schema::table('official_trips', function (Blueprint $table) {
            $table->dropColumn(['departure_odometer', 'arrival_odometer']);
        });
    }
};