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
        Schema::table('price_symbols', function (Blueprint $table) {
            $table->string('lowestPrice')->nullable()->after('slug'); // إضافة عمود القطاع
            $table->string('percentage_change_form_low_to_now')->nullable()->after('slug'); // إضافة عمود القطاع
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('price_symbols', function (Blueprint $table) {
            $table->dropColumn('lowestPrice');
        });
    }
};

