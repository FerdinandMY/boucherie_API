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
        Schema::create('stocks', function (Blueprint $table) {
            $table->id();

            // Référence au produit (viande, abats, etc.)
            $table->integer('product_id');

            // Référence au type de stock (frigo, congélateur...)
            $table->integer('type_stock_id');

            // Quantité disponible
            $table->decimal('quantity', 10, 2); // ex: 25.50 kg

            // Unité : kg, pièce, etc.
            $table->string('unit', 20)->default('kg');

            // Type de mouvement : entrée, sortie, ajustement
            $table->enum('movement_type', ['entrée', 'sortie', 'ajustement']);

            // Origine du mouvement : vente, achat, transformation, inventaire
            $table->string('source_type')->nullable();

            // ID de l'entité source si applicable (ex: sale_id, purchase_id)
            $table->unsignedBigInteger('source_id')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stocks');
    }
};
