<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Boucherie;
use App\Models\Distribution;
use App\Models\Reception;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReceptionFactory extends Factory
{
    protected $model = Reception::class;

    public function definition(): array
    {
        return [
            'distribution_id' => Distribution::factory(),
            'boucherie_id'    => Boucherie::factory(),
            'user_id'         => User::factory(),
            'quantite_recue'  => fake()->randomFloat(3, 1, 100),
            'date_reception'  => fake()->date(),
            'notes'           => null,
        ];
    }
}
