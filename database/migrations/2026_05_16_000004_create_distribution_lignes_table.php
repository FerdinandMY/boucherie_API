<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('distribution_lignes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('distribution_id')->constrained('distributions')->cascadeOnDelete();
            $table->string('categorie', 30);
            $table->decimal('poids_kg', 8, 2);
            $table->decimal('prix_par_kg', 10, 2)->nullable();
            $table->timestamps();

            $table->index(['distribution_id', 'categorie']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('distribution_lignes');
    }
};
