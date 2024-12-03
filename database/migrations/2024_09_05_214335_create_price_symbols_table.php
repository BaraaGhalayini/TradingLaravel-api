<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('price_symbols', function (Blueprint $table) {
            $table->id();
            $table->string('currency_name'); // اسم العملة الرقمية
            $table->decimal('current_price', 16, 8)->nullable(); // السعر الحالي
            $table->decimal('average_buy_price', 16, 8); // متوسط سعر الشراء
            $table->decimal('percentage_change', 5, 2)->nullable(); // نسبة التغير
            $table->decimal('quantity', 16, 8); // الكمية التي تم شراؤها
            $table->decimal('purchase_amount', 16, 2)->nullable(); // مبلغ الشراء
            $table->decimal('current_value', 16, 2)->nullable(); // قيمة المبلغ الآن
            $table->decimal('target', 16, 3)->nullable(); // سعر الهدف
            $table->decimal('afterSell', 16, 2)->nullable(); // قيمة المبلغ المفترض بعد البيع
            $table->timestamps(); // التواريخ الافتراضية (created_at, updated_at)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('price_symbols');
    }
};
