<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fournisseur_boucherie', function (Blueprint $table) {
            $table->foreignUuid('fournisseur_id')
                ->constrained('fournisseurs')
                ->cascadeOnDelete();
            $table->foreignUuid('boucherie_id')
                ->constrained('boucheries')
                ->cascadeOnDelete();
            $table->timestamps();

            $table->primary(['fournisseur_id', 'boucherie_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fournisseur_boucherie');
    }
};
