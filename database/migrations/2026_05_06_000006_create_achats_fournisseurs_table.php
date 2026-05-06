<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('achats_fournisseurs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('boucherie_id')->constrained('boucheries')->cascadeOnDelete();
            $table->foreignUuid('fournisseur_id')->constrained('fournisseurs')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('reference', 100)->nullable();
            $table->decimal('montant_total', 12, 2)->default(0);
            $table->date('date_achat');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['boucherie_id', 'fournisseur_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('achats_fournisseurs');
    }
};
