<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('payment_method', 50); // e.g., 'Kuraimi', 'Al-Najm', 'M-Pesa'
            $table->decimal('amount', 12, 2);
            $table->string('currency', 3)->default('YER');
            $table->string('transaction_id', 100)->unique()->nullable();
            $table->string('status', 20)->default('pending'); // pending, completed, failed
            $table->string('payment_for', 50); // e.g., 'subscription', 'refuel'
            $table->unsignedBigInteger('related_id')->nullable(); // subscription_id or refuel_id
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });

        Schema::create('station_ratings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('station_id')->constrained('gas_stations')->cascadeOnDelete();
            $table->unsignedTinyInteger('rating'); // 1-5
            $table->text('comment')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->unique(['user_id', 'station_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('station_ratings');
        Schema::dropIfExists('payments');
    }
};
