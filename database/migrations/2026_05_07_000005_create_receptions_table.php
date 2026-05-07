<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('receptions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('distribution_id')->unique()->constrained('distributions')->cascadeOnDelete();
            $table->foreignUuid('boucherie_id')->constrained('boucheries')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->decimal('quantite_recue', 10, 3);
            $table->date('date_reception');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('boucherie_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('receptions');
    }
};
