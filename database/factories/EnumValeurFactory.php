<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\EnumValeur;
use Illuminate\Database\Eloquent\Factories\Factory;

class EnumValeurFactory extends Factory
{
    protected $model = EnumValeur::class;

    public function definition(): array
    {
        return [
            'type'         => 'espece_animal',
            'valeur'       => fake()->unique()->word(),
            'libelle'      => fake()->word(),
            'systeme'      => false,
            'boucherie_id' => null,
            'ordre'        => 0,
        ];
    }

    public function typeVente(string $valeur = 'comptoir'): static
    {
        return $this->state([
            'type'    => 'type_vente',
            'valeur'  => $valeur,
            'libelle' => $valeur,
        ]);
    }

    public function especeAnimal(string $valeur = 'bovin'): static
    {
        return $this->state([
            'type'    => 'espece_animal',
            'valeur'  => $valeur,
            'libelle' => $valeur,
        ]);
    }
}
