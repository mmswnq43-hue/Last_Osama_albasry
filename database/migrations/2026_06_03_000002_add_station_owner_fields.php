<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('gas_stations', function (Blueprint $table) {
            $table->string('city', 100)->nullable()->after('phone');
            $table->string('district', 100)->nullable()->after('city');
            $table->string('license_number', 100)->nullable()->unique()->after('district');
            $table->date('license_issue_date')->nullable()->after('license_number');
            $table->date('license_expiry_date')->nullable()->after('license_issue_date');
            $table->unsignedInteger('pumps_count')->default(1)->after('license_expiry_date');
            $table->json('fuel_types')->nullable()->after('pumps_count');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('national_id', 50)->nullable()->unique()->after('phone');
            $table->text('address')->nullable()->after('national_id');
        });
    }

    public function down(): void
    {
        Schema::table('gas_stations', function (Blueprint $table) {
            $table->dropColumn(['city', 'district', 'license_number', 'license_issue_date', 'license_expiry_date', 'pumps_count', 'fuel_types']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['national_id', 'address']);
        });
    }
};
