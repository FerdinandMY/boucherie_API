<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Boucherie;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $boucherie = Boucherie::firstOrCreate(
            ['nom' => 'Boucherie Test'],
            [
                'adresse'   => '12 rue du Marché',
                'ville'     => 'Paris',
                'telephone' => '+33612345678',
                'actif'     => true,
            ]
        );

        $users = [
            [
                'name'     => 'Admin Test',
                'email'    => 'admin@test.com',
                'password' => 'password',
                'role'     => 'admin',
            ],
            [
                'name'     => 'Boucher Test',
                'email'    => 'boucher@test.com',
                'password' => 'password',
                'role'     => 'boucher',
            ],
            [
                'name'     => 'Caissier Test',
                'email'    => 'caissier@test.com',
                'password' => 'password',
                'role'     => 'caissier',
            ],
        ];

        foreach ($users as $data) {
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name'         => $data['name'],
                    'password'     => $data['password'],
                    'boucherie_id' => $boucherie->id,
                ]
            );

            $user->syncRoles([$data['role']]);
        }
    }
}
