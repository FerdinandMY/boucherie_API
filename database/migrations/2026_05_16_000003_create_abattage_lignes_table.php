<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('abattage_lignes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('abattage_id')->constrained('abattages')->cascadeOnDelete();
            $table->string('categorie', 30); // viande_rouge|abats|volaille|charcuterie|autre
            $table->decimal('poids_kg', 8, 2);
            $table->timestamps();

            $table->index(['abattage_id', 'categorie']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('abattage_lignes');
    }
};
