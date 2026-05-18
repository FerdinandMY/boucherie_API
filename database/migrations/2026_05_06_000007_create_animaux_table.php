<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('animaux', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('boucherie_id')->nullable()->constrained('boucheries')->nullOnDelete();
            $table->foreignUuid('fournisseur_id')->nullable()->constrained('fournisseurs')->nullOnDelete();
            $table->foreignUuid('achat_fournisseur_id')->nullable()->constrained('achats_fournisseurs')->nullOnDelete();
            $table->string('espece', 20);
            $table->string('numero_tag', 100)->nullable();
            $table->decimal('poids_vif_kg', 8, 2);
            $table->decimal('prix_achat', 10, 2)->nullable();
            $table->date('date_acquisition')->nullable();
            $table->string('statut', 20)->default('en_attente');
            $table->timestamps();

            $table->index(['boucherie_id', 'statut']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('animaux');
    }
};
