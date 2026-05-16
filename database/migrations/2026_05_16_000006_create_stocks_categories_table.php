<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stocks_categories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('boucherie_id')->constrained('boucheries')->cascadeOnDelete();
            $table->string('categorie', 30);
            $table->decimal('poids_kg_disponible', 10, 3)->default(0);
            $table->timestamps();

            $table->unique(['boucherie_id', 'categorie']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stocks_categories');
    }
};
