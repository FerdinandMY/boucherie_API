<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Boucherie;
use App\Models\Client;
use Illuminate\Database\Eloquent\Factories\Factory;

class ClientFactory extends Factory
{
    protected $model = Client::class;

    public function definition(): array
    {
        return [
            'boucherie_id' => Boucherie::factory(),
            'nom'          => fake()->company(),
            'telephone'    => fake()->phoneNumber(),
            'email'        => fake()->unique()->safeEmail(),
            'adresse'      => fake()->address(),
        ];
    }
}
