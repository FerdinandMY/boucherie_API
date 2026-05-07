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
        Schema::table('animaux', function (Blueprint $table) {
            $table->dropForeign(['boucherie_id']);
        });

        DB::statement('ALTER TABLE animaux MODIFY COLUMN boucherie_id CHAR(36) NULL');

        Schema::table('animaux', function (Blueprint $table) {
            $table->foreign('boucherie_id')->references('id')->on('boucheries')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('animaux', function (Blueprint $table) {
            $table->dropForeign(['boucherie_id']);
        });

        DB::statement('ALTER TABLE animaux MODIFY COLUMN boucherie_id CHAR(36) NOT NULL');

        Schema::table('animaux', function (Blueprint $table) {
            $table->foreign('boucherie_id')->references('id')->on('boucheries')->cascadeOnDelete();
        });
    }
};
