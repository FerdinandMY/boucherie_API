<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reception_lignes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('reception_id')->constrained('receptions')->cascadeOnDelete();
            $table->string('categorie', 30);
            $table->decimal('poids_kg_attendu', 8, 2)->nullable(); // issu de la distribution_ligne
            $table->decimal('poids_kg_recu', 8, 2);
            $table->timestamps();

            $table->index(['reception_id', 'categorie']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reception_lignes');
    }
};
