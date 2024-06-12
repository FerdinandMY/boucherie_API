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
        Schema::create('butchers', function (Blueprint $table) {
            // Identifiant unique pour chaque boucherie
            $table->id('id');

            // Nom de la boucherie
            $table->string('name', 100);

            // Adresse de la boucherie
            $table->string('address', 255);

            // Ville où se trouve la boucherie
            $table->string('city', 100);

            // Code postal de la ville
            $table->string('postal_code', 10);

            // Numéro de téléphone de la boucherie
            $table->string('phone', 20)->nullable();

            // Adresse e-mail de la boucherie
            $table->string('email', 100)->nullable();

            // Horaires d'ouverture de la boucherie
            $table->string('opening_hours', 255)->nullable();

            // Date de création de la boucherie
            $table->date('created_at')->nullable();

            // URL du site web de la boucherie
            $table->string('website', 100)->nullable();

            // Nom du propriétaire de la boucherie
            $table->string('owner', 100)->nullable();

            // Spécialités de la boucherie
            $table->string('specialties', 255)->nullable();

            // Note moyenne des avis clients
            $table->float('average_rating')->nullable();

            // Nombre d'avis clients
            $table->integer('review_count')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('butchers');
    }
};
