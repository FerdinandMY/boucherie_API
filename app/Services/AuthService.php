<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthService
{
    public function register(array $data): array
    {
        $role = $data['role'] ?? 'boucher';
        unset($data['role']);

        $user = User::create([
            'name'         => $data['name'],
            'email'        => $data['email'],
            'password'     => Hash::make($data['password']),
            'boucherie_id' => $data['boucherie_id'] ?? null,
        ]);

        $user->assignRole($role);

        $token = $user->createToken('api-token')->plainTextToken;

        return ['user' => $user->load('roles'), 'token' => $token];
    }

    public function login(array $data): array
    {
        $user = User::where('email', $data['email'])->first();

        if (!$user || !Hash::check($data['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Adresse email ou mot de passe incorrect.'],
            ]);
        }

        $user->tokens()->delete();
        $token = $user->createToken('api-token')->plainTextToken;

        return ['user' => $user->load('roles'), 'token' => $token];
    }

    public function logout(User $user): void
    {
        $user->tokens()->delete();
    }
}
