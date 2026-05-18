<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\AchatFournisseur;
use App\Models\Boucherie;
use App\Models\Fournisseur;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AchatFournisseurFactory extends Factory
{
    protected $model = AchatFournisseur::class;

    public function definition(): array
    {
        return [
            'boucherie_id'  => Boucherie::factory(),
            'fournisseur_id' => Fournisseur::factory(),
            'user_id'       => User::factory(),
            'reference'     => fake()->unique()->numerify('REF-####'),
            'montant_total' => fake()->randomFloat(2, 10000, 1000000),
            'date_achat'    => fake()->date(),
            'notes'         => null,
        ];
    }
}
