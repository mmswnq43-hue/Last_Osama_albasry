<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('full_name', 100);
            $table->string('phone', 20)->unique();
            $table->string('password_hash', 255);
            $table->string('vehicle_type', 50)->nullable();
            $table->string('engine_number', 50)->nullable();
            $table->string('user_role', 20)->default('customer');
            $table->string('qr_code', 100)->unique()->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('phone_verified')->default(false);
            $table->boolean('two_factor_enabled')->default(false);
            $table->boolean('account_locked')->default(false);
            $table->float('last_location_lat')->nullable();
            $table->float('last_location_lon')->nullable();
            $table->dateTime('last_location_update')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
        });

        if (in_array(DB::getDriverName(), ['mysql', 'mariadb', 'pgsql'], true)) {
            DB::statement("ALTER TABLE users ADD CONSTRAINT user_role_check_v3 CHECK (user_role IN ('customer', 'station_owner', 'car_wash_owner', 'maintenance_owner', 'admin', 'station_worker', 'car_wash_worker', 'maintenance_worker'))");
        }

        Schema::create('gas_stations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_id')->constrained('users')->cascadeOnDelete();
            $table->string('station_name', 100);
            $table->string('commercial_register', 50)->unique();
            $table->string('location', 255)->index();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->string('station_code', 20)->unique();
            $table->boolean('is_active')->default(true);
            $table->timestamp('created_at')->useCurrent();
        });

        Schema::create('car_wash_centers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_id')->constrained('users')->cascadeOnDelete();
            $table->string('center_name', 100);
            $table->string('commercial_register', 50)->unique();
            $table->string('location', 255)->index();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->string('center_code', 20)->unique();
            $table->boolean('is_active')->default(true);
            $table->timestamp('created_at')->useCurrent();
        });

        Schema::create('maintenance_centers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_id')->constrained('users')->cascadeOnDelete();
            $table->string('center_name', 100);
            $table->string('commercial_register', 50)->unique();
            $table->string('location', 255)->index();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->string('center_code', 20)->unique();
            $table->string('specialization', 100)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('created_at')->useCurrent();
        });

        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('station_id')->nullable()->constrained('gas_stations')->cascadeOnDelete();
            $table->foreignId('car_wash_center_id')->nullable()->constrained('car_wash_centers')->cascadeOnDelete();
            $table->foreignId('maintenance_center_id')->nullable()->constrained('maintenance_centers')->cascadeOnDelete();
            $table->string('employee_code', 20)->unique();
            $table->string('position', 50)->nullable();
            $table->dateTime('hire_date');
            $table->decimal('salary', 10, 2)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('created_at')->useCurrent();
        });

        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('plan_type', 20);
            $table->decimal('price', 10, 2);
            $table->integer('discount_percent');
            $table->dateTime('start_date');
            $table->dateTime('end_date')->index();
            $table->string('status', 20)->default('active');
            $table->string('payment_receipt_image', 255)->nullable();
            $table->integer('remaining_cylinders')->default(0);
            $table->integer('remaining_car_washes')->default(0);
            $table->integer('remaining_maintenance')->default(0);
            $table->text('notes')->nullable();
            $table->decimal('monthly_liters', 10, 2)->default(0.00);
            $table->dateTime('last_reset_date')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });

        Schema::create('refuels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('station_id')->constrained('gas_stations');
            $table->foreignId('subscription_id')->nullable()->constrained('subscriptions');
            $table->foreignId('employee_id')->nullable()->constrained('employees');
            $table->decimal('liters', 10, 2);
            $table->decimal('price_per_liter', 10, 2);
            $table->decimal('total_before_discount', 10, 2);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('final_price', 10, 2);
            $table->string('qr_code_used', 100);
            $table->timestamp('refuel_date')->useCurrent()->index();
            $table->timestamp('created_at')->useCurrent();
        });

        Schema::create('car_washes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('center_id')->constrained('car_wash_centers')->cascadeOnDelete();
            $table->foreignId('subscription_id')->nullable()->constrained('subscriptions');
            $table->foreignId('employee_id')->nullable()->constrained('employees');
            $table->string('wash_type', 50);
            $table->string('qr_code_used', 100);
            $table->timestamp('wash_date')->useCurrent()->index();
            $table->dateTime('completed_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });

        Schema::create('maintenance_services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('center_id')->constrained('maintenance_centers')->cascadeOnDelete();
            $table->foreignId('subscription_id')->nullable()->constrained('subscriptions');
            $table->foreignId('employee_id')->nullable()->constrained('employees');
            $table->string('service_type', 100);
            $table->text('description')->nullable();
            $table->decimal('cost', 10, 2)->nullable();
            $table->string('qr_code_used', 100);
            $table->timestamp('service_date')->useCurrent()->index();
            $table->dateTime('completed_at')->nullable();
            $table->dateTime('next_service_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });

        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->foreignId('station_id')->nullable()->constrained('gas_stations')->cascadeOnDelete();
            $table->foreignId('car_wash_center_id')->nullable()->constrained('car_wash_centers')->cascadeOnDelete();
            $table->foreignId('maintenance_center_id')->nullable()->constrained('maintenance_centers')->cascadeOnDelete();
            $table->foreignId('sender_id')->nullable()->constrained('users');
            $table->string('title', 255);
            $table->text('message');
            $table->string('notification_type', 50);
            $table->boolean('is_read')->default(false);
            $table->boolean('is_important')->default(false);
            $table->timestamp('created_at')->useCurrent();
            $table->dateTime('read_at')->nullable();
        });

        Schema::create('security_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->string('log_type', 20);
            $table->string('session_id', 64)->nullable();
            $table->string('qr_code', 128)->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->float('user_lat')->nullable();
            $table->float('user_lon')->nullable();
            $table->float('service_lat')->nullable();
            $table->float('service_lon')->nullable();
            $table->float('distance_meters')->nullable();
            $table->boolean('is_successful')->default(true);
            $table->text('error_message')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });

        Schema::create('support_tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('title', 255);
            $table->text('description');
            $table->string('status', 20)->default('open');
            $table->string('priority', 20)->default('normal');
            $table->text('admin_response')->nullable();
            $table->dateTime('resolved_at')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
        });

        Schema::create('electronic_cards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('card_number', 50)->unique();
            $table->dateTime('generated_at')->useCurrent();
            $table->decimal('monthly_liters_at_generation', 10, 2);
            $table->boolean('is_used')->default(false);
            $table->dateTime('used_at')->nullable();
            $table->foreignId('priority_station_id')->nullable()->constrained('gas_stations');
            $table->dateTime('expires_at')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('electronic_cards');
        Schema::dropIfExists('support_tickets');
        Schema::dropIfExists('security_logs');
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('maintenance_services');
        Schema::dropIfExists('car_washes');
        Schema::dropIfExists('refuels');
        Schema::dropIfExists('subscriptions');
        Schema::dropIfExists('employees');
        Schema::dropIfExists('maintenance_centers');
        Schema::dropIfExists('car_wash_centers');
        Schema::dropIfExists('gas_stations');
        Schema::dropIfExists('users');
    }
};
