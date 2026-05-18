<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Boucherie;
use App\Models\Produit;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProduitFactory extends Factory
{
    protected $model = Produit::class;

    public function definition(): array
    {
        return [
            'boucherie_id'  => Boucherie::factory(),
            'nom'           => fake()->words(3, true),
            'description'   => fake()->sentence(),
            'categorie'     => fake()->randomElement(['boeuf', 'mouton', 'volaille', 'porc']),
            'unite'         => 'kg',
            'prix_unitaire' => fake()->randomFloat(2, 500, 10000),
        ];
    }
}
