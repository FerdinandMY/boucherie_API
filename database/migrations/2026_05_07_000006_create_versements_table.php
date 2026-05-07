<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('versements', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('boucherie_id')->constrained('boucheries')->cascadeOnDelete();
            $table->foreignId('fournisseur_user_id')->constrained('users')->cascadeOnDelete();
            $table->decimal('montant', 12, 2);
            $table->string('mode_paiement', 50);
            $table->date('date_versement');
            $table->string('reference', 100)->nullable();
            $table->string('statut', 20)->default('en_attente'); // en_attente, valide, rejete
            $table->text('motif_rejet')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('valide_par')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('valide_le')->nullable();
            $table->timestamps();

            $table->index(['boucherie_id', 'statut']);
            $table->index(['fournisseur_user_id', 'statut']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('versements');
    }
};
