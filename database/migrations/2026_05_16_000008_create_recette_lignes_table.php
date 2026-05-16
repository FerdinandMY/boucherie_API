<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recette_lignes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('recette_id')
                ->constrained('recettes_journalieres')
                ->cascadeOnDelete();
            $table->string('categorie', 30);
            $table->decimal('poids_kg_vendu', 8, 2);
            $table->decimal('prix_par_kg', 10, 2);
            $table->decimal('montant', 12, 2); // = poids_kg_vendu × prix_par_kg
            $table->timestamps();

            $table->index(['recette_id', 'categorie']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recette_lignes');
    }
};
