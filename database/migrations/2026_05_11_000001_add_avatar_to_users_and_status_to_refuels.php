<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // إضافة حقل avatar لجدول المستخدمين
        Schema::table('users', function (Blueprint $table) {
            $table->string('avatar', 500)->nullable()->after('engine_number');
        });

        // إضافة حقل status لجدول التعبئات
        Schema::table('refuels', function (Blueprint $table) {
            $table->string('status', 20)->default('completed')->after('final_price');
        });

        // إضافة حقل rating لجدول محطات الغاز (مرحلة 5)
        Schema::table('gas_stations', function (Blueprint $table) {
            $table->decimal('rating', 3, 2)->default(0.00)->after('is_active');
            $table->unsignedInteger('rating_count')->default(0)->after('rating');
            $table->json('services')->nullable()->after('rating_count'); // مثال: ["refuel","car_wash"]
        });

        // إضافة حقل is_open لجدول محطات الغاز
        Schema::table('gas_stations', function (Blueprint $table) {
            $table->boolean('is_open')->default(true)->after('is_active');
        });

        // إضافة حقل phone لجدول محطات الغاز
        Schema::table('gas_stations', function (Blueprint $table) {
            $table->string('phone', 20)->nullable()->after('location');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('avatar');
        });

        Schema::table('refuels', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        Schema::table('gas_stations', function (Blueprint $table) {
            $table->dropColumn(['rating', 'rating_count', 'services', 'is_open', 'phone']);
        });
    }
};
