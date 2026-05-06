<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('paiements', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('vente_id')->constrained('ventes')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('mode_paiement', 20);
            $table->decimal('montant', 12, 2);
            $table->timestamp('date_paiement')->useCurrent();
            $table->timestamps();

            $table->index('vente_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('paiements');
    }
};
