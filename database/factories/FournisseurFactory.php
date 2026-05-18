<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Fournisseur;
use Illuminate\Database\Eloquent\Factories\Factory;

class FournisseurFactory extends Factory
{
    protected $model = Fournisseur::class;

    public function definition(): array
    {
        return [
            'boucherie_id' => null,
            'user_id'      => null,
            'nom'          => fake()->company(),
            'contact'      => fake()->name(),
            'telephone'    => fake()->phoneNumber(),
            'email'        => fake()->unique()->safeEmail(),
            'adresse'      => fake()->address(),
        ];
    }
}
