<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('livraisons', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('vente_id')->constrained('ventes')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('adresse_livraison', 255);
            $table->string('statut', 20)->default('en_attente');
            $table->timestamp('date_prevue')->nullable();
            $table->timestamp('date_effective')->nullable();
            $table->timestamps();

            $table->index(['vente_id', 'statut']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('livraisons');
    }
};
