<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('typestocks', function (Blueprint $table) {
            $table->id(); // Identifiant unique pour chaque type de stock

            // Nom du type de stock
            $table->string('type_name', 100);

            // Description du type de stock
            $table->text('description')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('typestocks');
    }
};
