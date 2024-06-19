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
        Schema::create('sales', function (Blueprint $table) {
            $table->id(); // Identifiant unique pour chaque vente

            // Référence à l'identifiant de la boucherie
            $table->unsignedBigInteger('butcher_shop_id');
            $table->foreign('butcher_id')->references('id')->on('butchers');

            // Référence à l'identifiant du produit
            $table->unsignedBigInteger('product_id');
            $table->foreign('product_id')->references('id')->on('stocks');

            // Quantité du produit vendu
            $table->integer('quantity');

            // Prix unitaire du produit vendu
            $table->decimal('unit_price', 10, 2);

            // Prix total de la vente
            $table->decimal('total_price', 10, 2);

            // Date de la vente
            $table->dateTime('sale_date');

            // Nom du client
            $table->string('customer_name', 100)->nullable();

            // E-mail du client
            $table->string('customer_email', 100)->nullable();

            // Méthode de paiement (par exemple, espèces, carte de crédit)
            $table->string('payment_method', 50);

            // Remarques ou commentaires additionnels sur la vente
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
