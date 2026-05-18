<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::connection()->getDriverName() === 'sqlite') {
            return;
        }

        Schema::table('fournisseurs', function (Blueprint $table) {
            // Supprimer la contrainte FK avant de modifier la colonne
            $table->dropForeign(['boucherie_id']);

            $table->foreignUuid('boucherie_id')
                ->nullable()
                ->change();

            // Re-créer la FK en version nullable
            $table->foreign('boucherie_id')
                ->references('id')
                ->on('boucheries')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('fournisseurs', function (Blueprint $table) {
            $table->dropForeign(['boucherie_id']);

            $table->foreignUuid('boucherie_id')
                ->nullable(false)
                ->change();

            $table->foreign('boucherie_id')
                ->references('id')
                ->on('boucheries')
                ->cascadeOnDelete();
        });
    }
};
