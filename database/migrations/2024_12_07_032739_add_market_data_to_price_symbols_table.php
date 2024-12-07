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
            $table->integer('market_cap_rank')->nullable()->after('quantity');
            $table->decimal('market_cap', 20, 2)->nullable()->after('market_cap_rank');
            $table->decimal('volume_24h', 20, 2)->nullable()->after('market_cap');
            $table->decimal('price_change_24h', 10, 2)->nullable()->after('volume_24h');
            $table->decimal('circulating_supply', 20, 2)->nullable()->after('price_change_24h');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('price_symbols', function (Blueprint $table) {
            $table->dropColumn([
                'market_cap_rank',
                'market_cap',
                'volume_24h',
                'price_change_24h',
                'circulating_supply',
            ]);
        });
    }
};
