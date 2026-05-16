<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recettes_journalieres', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('boucherie_id')->constrained('boucheries')->cascadeOnDelete();
            $table->foreignUuid('fournisseur_id')->constrained('fournisseurs')->cascadeOnDelete();
            $table->date('date');
            $table->decimal('montant_total', 12, 2)->default(0); // Σ des lignes
            $table->decimal('montant_verse', 12, 2)->default(0); // versé au fournisseur ce jour
            $table->string('statut_versement', 20)->default('en_attente'); // en_attente|valide|rejete
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['boucherie_id', 'date']); // une recette par jour par boucherie
            $table->index(['boucherie_id', 'statut_versement']);
            $table->index(['fournisseur_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recettes_journalieres');
    }
};
