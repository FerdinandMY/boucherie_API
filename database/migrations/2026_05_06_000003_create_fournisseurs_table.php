<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fournisseurs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('boucherie_id')->nullable()->constrained('boucheries')->nullOnDelete();
            $table->string('nom', 150);
            $table->string('contact', 150)->nullable();
            $table->string('telephone', 20)->nullable();
            $table->string('email', 150)->nullable();
            $table->string('adresse', 500)->nullable();
            $table->timestamps();

            $table->index('boucherie_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fournisseurs');
    }
};
