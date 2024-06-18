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
        Schema::create('currencies', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('name');
            $table->integer('entry_price');
            $table->integer('tp1');
            $table->integer('tp2');
            $table->integer('tp3');
            $table->integer('tp4');
            $table->integer('tp5');
            $table->integer('sl');
            $table->enum('status', ['buy', 'sell']);
            $table->string('sgy_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('currencies');
    }
};
