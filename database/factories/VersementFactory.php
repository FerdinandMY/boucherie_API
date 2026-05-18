<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Boucherie;
use App\Models\User;
use App\Models\Versement;
use Illuminate\Database\Eloquent\Factories\Factory;

class VersementFactory extends Factory
{
    protected $model = Versement::class;

    public function definition(): array
    {
        return [
            'boucherie_id'        => Boucherie::factory(),
            'fournisseur_user_id' => User::factory(),
            'montant'             => fake()->randomFloat(2, 5000, 500000),
            'mode_paiement'       => 'mobile_money',
            'date_versement'      => fake()->date(),
            'reference'           => fake()->unique()->numerify('TXN-####'),
            'statut'              => 'en_attente',
            'notes'               => null,
        ];
    }

    public function valide(): static
    {
        return $this->state(['statut' => 'valide']);
    }
}
