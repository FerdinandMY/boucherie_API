<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Boucherie;
use Illuminate\Database\Eloquent\Factories\Factory;

class BoucherieFactory extends Factory
{
    protected $model = Boucherie::class;

    public function definition(): array
    {
        return [
            'nom'       => fake()->company(),
            'adresse'   => fake()->streetAddress(),
            'ville'     => fake()->city(),
            'telephone' => fake()->phoneNumber(),
            'actif'     => true,
        ];
    }
}
