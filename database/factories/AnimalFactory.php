<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Animal;
use App\Models\Boucherie;
use Illuminate\Database\Eloquent\Factories\Factory;

class AnimalFactory extends Factory
{
    protected $model = Animal::class;

    public function definition(): array
    {
        return [
            'boucherie_id'     => Boucherie::factory(),
            'fournisseur_id'   => null,
            'achat_fournisseur_id' => null,
            'espece'           => 'bovin',
            'numero_tag'       => fake()->unique()->numerify('TAG-####'),
            'poids_vif_kg'     => fake()->randomFloat(2, 100, 600),
            'prix_achat'       => fake()->randomFloat(2, 50000, 500000),
            'date_acquisition' => fake()->date(),
            'statut'           => 'en_attente',
        ];
    }

    public function abattu(): static
    {
        return $this->state(['statut' => 'abattu']);
    }
}
