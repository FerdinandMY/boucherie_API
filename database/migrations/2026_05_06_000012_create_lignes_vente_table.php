<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lignes_vente', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('vente_id')->constrained('ventes')->cascadeOnDelete();
            $table->foreignUuid('produit_id')->constrained('produits')->cascadeOnDelete();
            $table->decimal('quantite', 10, 3);
            $table->decimal('prix_unitaire', 10, 2);
            $table->decimal('sous_total', 12, 2);
            $table->timestamps();

            $table->index('vente_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lignes_vente');
    }
};
