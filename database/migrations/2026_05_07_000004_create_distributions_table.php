<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('distributions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('abattage_id')->constrained('abattages')->cascadeOnDelete();
            $table->foreignId('fournisseur_user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignUuid('boucherie_id')->constrained('boucheries')->cascadeOnDelete();
            $table->foreignUuid('produit_id')->constrained('produits')->cascadeOnDelete();
            $table->decimal('quantite', 10, 3);
            $table->string('statut', 20)->default('en_attente'); // en_attente, acceptee, rejetee
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['abattage_id', 'statut']);
            $table->index(['boucherie_id', 'statut']);
            $table->index('fournisseur_user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('distributions');
    }
};
