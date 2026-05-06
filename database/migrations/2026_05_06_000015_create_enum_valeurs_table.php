<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('enum_valeurs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('type', 50);
            $table->string('valeur', 100);
            $table->string('libelle', 150);
            $table->boolean('systeme')->default(false);
            $table->foreignUuid('boucherie_id')->nullable()->constrained('boucheries')->nullOnDelete();
            $table->unsignedSmallInteger('ordre')->default(0);
            $table->timestamps();

            $table->index('type');
            $table->index(['type', 'boucherie_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('enum_valeurs');
    }
};
