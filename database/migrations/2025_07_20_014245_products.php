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
        Schema::create('products', function (Blueprint $table) {
            $table->id(); // Identifiant unique

            // Nom du produit (ex: Boeuf haché, Foie de veau)
            $table->string('name', 100);

            // Description optionnelle du produit
            $table->text('description')->nullable();

            // Prix unitaire (au kg ou à la pièce)
            $table->decimal('price', 10, 2)->nullable();

            // Unité de vente par défaut (kg, pièce, etc.)
            $table->string('unit', 20)->default('kg');

            // Référence interne ou code barre
            $table->string('reference', 50)->unique()->nullable();

            // Statut actif/inactif (ex: visible en boutique)
            $table->boolean('is_active')->default(true);

            $table->timestamps(); // created_at / updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
