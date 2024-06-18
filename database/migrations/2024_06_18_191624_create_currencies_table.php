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
            $table->string('name', 255);
            $table->integer('entry_price')->nullable();
            $table->integer('tp1')->nullable();
            $table->integer('tp2')->nullable();
            $table->integer('tp3')->nullable();
            $table->integer('tp4')->nullable();
            $table->integer('tp5')->nullable();
            $table->integer('sl')->nullable();
            $table->enum('status', ['buy', 'sell']);
            $table->string('sgy_type')->nullable();
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
