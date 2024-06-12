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
            $table->id(); // Identifiant unique pour chaque enregistrement de stock

            // Référence à l'identifiant de la boucherie
            $table->unsignedBigInteger('butcher_shop_id');
            $table->foreign('butcher_shop_id')->references('id')->on('butcher_shops');

            // Nom du produit en stock
            $table->string('product_name', 100);

            // Quantité du produit en stock
            $table->integer('quantity');

            // Unité de mesure pour le produit (par exemple, kg, pièces)
            $table->string('unit', 20);

            // Date d'ajout du produit en stock
            $table->date('date_added');

            // Date d'expiration du produit
            $table->date('expiration_date')->nullable();

            // Prix par unité du produit
            $table->decimal('price_per_unit', 10, 2);

            // Nom du fournisseur du produit
            $table->string('supplier', 100)->nullable();

            // Remarques ou commentaires additionnels sur le stock
            $table->text('remarks')->nullable();
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
