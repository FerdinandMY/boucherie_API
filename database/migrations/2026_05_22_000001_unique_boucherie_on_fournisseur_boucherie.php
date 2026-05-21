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
        if (! Schema::hasTable('fournisseur_boucherie')) {
            return;
        }

        $duplicateBoucherieIds = DB::table('fournisseur_boucherie')
            ->select('boucherie_id')
            ->groupBy('boucherie_id')
            ->havingRaw('COUNT(*) > 1')
            ->pluck('boucherie_id');

        foreach ($duplicateBoucherieIds as $boucherieId) {
            $rows = DB::table('fournisseur_boucherie')
                ->where('boucherie_id', $boucherieId)
                ->orderBy('created_at')
                ->get();

            $keepFournisseurId = $rows->first()->fournisseur_id;

            DB::table('fournisseur_boucherie')
                ->where('boucherie_id', $boucherieId)
                ->where('fournisseur_id', '!=', $keepFournisseurId)
                ->delete();
        }

        Schema::table('fournisseur_boucherie', function (Blueprint $table) {
            $table->unique('boucherie_id');
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('fournisseur_boucherie')) {
            return;
        }

        Schema::table('fournisseur_boucherie', function (Blueprint $table) {
            $table->dropUnique(['boucherie_id']);
        });
    }
};
