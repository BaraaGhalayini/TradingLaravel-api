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
            $table->string('slug')->nullable();
            $table->string('sector')->nullable(); // إضافة عمود القطاع
            $table->decimal('current_price', 16, 8)->nullable(); // السعر الحالي
            $table->decimal('average_buy_price', 16, 8); // متوسط سعر الشراء
            $table->decimal('percentage_change', 5, 2)->nullable(); // نسبة التغير
            $table->decimal('quantity', 16, 8); // الكمية التي تم شراؤها
            $table->integer('market_cap_rank')->nullable();
            $table->decimal('market_cap', 20, 2)->nullable();
            $table->decimal('volume_24h', 20, 2)->nullable();
            $table->decimal('price_change_24h', 10, 2)->nullable();
            $table->decimal('circulating_supply', 20, 2)->nullable();
            $table->decimal('purchase_amount', 16, 2)->nullable(); // مبلغ الشراء
            $table->decimal('current_value', 16, 2)->nullable(); // قيمة المبلغ الآن
            $table->decimal('target', 16, 3)->nullable(); // سعر الهدف
            $table->decimal('afterSell', 16, 2)->nullable(); // قيمة المبلغ المفترض بعد البيع
            $table->decimal('lowestPrice', 16, 2)->nullable()->after('slug'); // إضافة عمود القطاع
            $table->string('percentage_change_form_low_to_now')->nullable()->after('slug'); // إضافة عمود القطاع

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
