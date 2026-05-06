<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('produits', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('boucherie_id')->constrained('boucheries')->cascadeOnDelete();
            $table->string('nom', 150);
            $table->text('description')->nullable();
            $table->string('categorie', 30);
            $table->string('unite', 10);
            $table->decimal('prix_unitaire', 10, 2);
            $table->timestamps();

            $table->index('boucherie_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('produits');
    }
};
