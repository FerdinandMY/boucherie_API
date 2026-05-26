<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // clients.boucherie_id — FK sans index explicite (manquant sur PostgreSQL)
        Schema::table('clients', function (Blueprint $table) {
            $table->index('boucherie_id');
        });

        // achats_fournisseurs.date_achat — utile pour les range queries par période
        Schema::table('achats_fournisseurs', function (Blueprint $table) {
            $table->index('date_achat');
        });
    }

    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropIndex(['boucherie_id']);
        });

        Schema::table('achats_fournisseurs', function (Blueprint $table) {
            $table->dropIndex(['date_achat']);
        });
    }
};
