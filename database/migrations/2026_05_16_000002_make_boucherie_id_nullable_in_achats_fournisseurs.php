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

        Schema::table('achats_fournisseurs', function (Blueprint $table) {
            $table->dropForeign(['boucherie_id']);

            $table->foreignUuid('boucherie_id')
                ->nullable()
                ->change();

            $table->foreign('boucherie_id')
                ->references('id')
                ->on('boucheries')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('achats_fournisseurs', function (Blueprint $table) {
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
