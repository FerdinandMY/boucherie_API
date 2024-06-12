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
        Schema::create('payouts', function (Blueprint $table) {
            $table->id(); // Identifiant unique pour chaque versement

            // Référence à l'identifiant de la vente
            $table->unsignedBigInteger('sale_id');
            $table->foreign('sale_id')->references('id')->on('sales');

            // Montant du versement
            $table->decimal('amount', 10, 2);

            // Date du versement
            $table->dateTime('payment_date');

            // Méthode de paiement (par exemple, espèces, carte de crédit)
            $table->string('payment_method', 50);

            // Statut du paiement (par exemple, payé, en attente, annulé)
            $table->string('payment_status', 50);

            // Remarques ou commentaires additionnels sur le versement
            $table->text('remarks')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payouts');
    }
};
