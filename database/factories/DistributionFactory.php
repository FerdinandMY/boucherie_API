<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Abattage;
use App\Models\Boucherie;
use App\Models\Distribution;
use App\Models\Produit;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class DistributionFactory extends Factory
{
    protected $model = Distribution::class;

    public function definition(): array
    {
        return [
            'abattage_id'         => Abattage::factory(),
            'fournisseur_user_id' => User::factory(),
            'boucherie_id'        => Boucherie::factory(),
            'produit_id'          => Produit::factory(),
            'quantite'            => fake()->randomFloat(3, 1, 100),
            'statut'              => 'en_attente',
            'notes'               => null,
        ];
    }

    public function acceptee(): static
    {
        return $this->state(['statut' => 'acceptee']);
    }
}
