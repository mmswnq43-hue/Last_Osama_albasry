<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('gas_stations', function (Blueprint $table) {
            $table->string('commercial_register', 50)->nullable()->change();
        });
        Schema::table('car_wash_centers', function (Blueprint $table) {
            $table->string('commercial_register', 50)->nullable()->change();
        });
        Schema::table('maintenance_centers', function (Blueprint $table) {
            $table->string('commercial_register', 50)->nullable()->change();
        });
    }

    public function down(): void
    {
        // Intentionally left empty - reverting to NOT NULL would break existing data
    }
};
