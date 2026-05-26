<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Boucherie;
use App\Models\Fournisseur;
use App\Models\RecetteJournaliere;
use Illuminate\Database\Eloquent\Factories\Factory;

class RecetteJournaliereFactory extends Factory
{
    protected $model = RecetteJournaliere::class;

    public function definition(): array
    {
        return [
            'boucherie_id'     => Boucherie::factory(),
            'fournisseur_id'   => Fournisseur::factory(),
            'date'             => fake()->unique()->date(),
            'montant_total'    => fake()->randomFloat(2, 50000, 500000),
            'montant_verse'    => fake()->randomFloat(2, 0, 50000),
            'statut_versement' => 'en_attente',
            'notes'            => null,
        ];
    }
}
