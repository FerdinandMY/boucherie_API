<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Abattage;
use App\Models\Animal;
use App\Models\Boucherie;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AbattageFactory extends Factory
{
    protected $model = Abattage::class;

    public function definition(): array
    {
        return [
            'animal_id'         => Animal::factory(),
            'user_id'           => User::factory(),
            'boucherie_id'      => Boucherie::factory(),
            'date_abattage'     => fake()->date(),
            'poids_carcasse_kg' => fake()->randomFloat(2, 50, 400),
            'rendement_pct'     => fake()->randomFloat(2, 40, 70),
            'notes'             => null,
        ];
    }
}
