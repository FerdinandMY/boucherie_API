<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('butcherstocks');
        Schema::dropIfExists('stocks');

        Schema::create('stocks', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('boucherie_id')->constrained('boucheries')->cascadeOnDelete();
            $table->foreignUuid('produit_id')->constrained('produits')->cascadeOnDelete();
            $table->foreignUuid('abattage_id')->nullable()->constrained('abattages')->nullOnDelete();
            $table->decimal('quantite', 10, 3)->default(0);
            $table->decimal('seuil_alerte', 10, 3)->default(0);
            $table->timestamps();

            $table->unique(['boucherie_id', 'produit_id']);
            $table->index(['boucherie_id', 'quantite']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stocks');
    }
};
