<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Boucherie;
use App\Models\Produit;
use App\Models\Stock;
use Illuminate\Database\Eloquent\Factories\Factory;

class StockFactory extends Factory
{
    protected $model = Stock::class;

    public function definition(): array
    {
        return [
            'boucherie_id' => Boucherie::factory(),
            'produit_id'   => Produit::factory(),
            'abattage_id'  => null,
            'quantite'     => fake()->randomFloat(3, 5, 200),
            'seuil_alerte' => 5.0,
        ];
    }
}
