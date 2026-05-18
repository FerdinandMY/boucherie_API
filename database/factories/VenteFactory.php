<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Boucherie;
use App\Models\User;
use App\Models\Vente;
use Illuminate\Database\Eloquent\Factories\Factory;

class VenteFactory extends Factory
{
    protected $model = Vente::class;

    public function definition(): array
    {
        return [
            'boucherie_id'  => Boucherie::factory(),
            'client_id'     => null,
            'user_id'       => User::factory(),
            'type_vente'    => 'comptoir',
            'statut'        => 'en_cours',
            'montant_total' => fake()->randomFloat(2, 1000, 100000),
            'notes'         => null,
        ];
    }

    public function annulee(): static
    {
        return $this->state(['statut' => 'annulee']);
    }
}
