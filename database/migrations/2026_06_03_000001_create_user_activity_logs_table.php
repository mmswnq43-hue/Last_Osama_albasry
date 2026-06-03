<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('user_activity_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('action_type'); // refuel, subscription, car_wash, maintenance, login, setting_change
            $table->string('title');
            $table->text('description')->nullable();
            $table->json('meta')->nullable(); // extra data
            $table->string('icon')->nullable(); // emoji or icon name
            $table->string('color')->nullable(); // green, blue, orange, red
            $table->timestamp('created_at')->useCurrent();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }
    public function down(): void { Schema::dropIfExists('user_activity_logs'); }
};
