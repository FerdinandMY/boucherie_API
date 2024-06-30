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
        Schema::create('butcherstocks', function (Blueprint $table) {
            $table->id();

            $table->foreignId('butcher_id')->constrained()->onDelete('cascade')->on('butchers');

            $table->foreignId('stock_id')->constrained()->onDelete('cascade')->on('stocks');

            // Quantité du produit en stock
            $table->integer('quantity');
            //Prix de viande en stock
            $table->integer('price');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('butcherstocks');
    }
};
