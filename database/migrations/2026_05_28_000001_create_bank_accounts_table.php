<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bank_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('bank_name', 100);          // اسم البنك
            $table->string('account_name', 150);        // اسم صاحب الحساب
            $table->string('account_number', 60);       // رقم الحساب
            $table->string('iban', 60)->nullable();     // الآيبان (اختياري)
            $table->string('currency', 10)->default('YER'); // العملة
            $table->string('notes', 255)->nullable();   // ملاحظات
            $table->boolean('is_active')->default(true);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bank_accounts');
    }
};
