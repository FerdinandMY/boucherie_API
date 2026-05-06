<?php

declare(strict_types=1);

use App\Models\User;
use Laravel\Sanctum\Sanctum;

describe('POST /api/v1/auth/register', function () {
    it('inscrit un utilisateur avec des données valides', function () {
        $this->postJson('/api/v1/auth/register', [
            'name'                  => 'Jean Dupont',
            'email'                 => 'jean@example.com',
            'password'              => 'secret123',
            'password_confirmation' => 'secret123',
        ])->assertCreated()
          ->assertJsonStructure([
              'data'  => ['id', 'name', 'email', 'created_at'],
              'token',
              'message',
          ]);
    });

    it('retourne 422 si email déjà utilisé', function () {
        User::factory()->create(['email' => 'jean@example.com']);

        $this->postJson('/api/v1/auth/register', [
            'name'                  => 'Jean',
            'email'                 => 'jean@example.com',
            'password'              => 'secret123',
            'password_confirmation' => 'secret123',
        ])->assertUnprocessable()
          ->assertJsonStructure(['message', 'errors']);
    });

    it('retourne 422 si les champs obligatoires manquent', function () {
        $this->postJson('/api/v1/auth/register', [])
            ->assertUnprocessable()
            ->assertJsonStructure(['message', 'errors']);
    });
});

describe('POST /api/v1/auth/login', function () {
    it('connecte un utilisateur avec des identifiants valides', function () {
        $user = User::factory()->create();

        $this->postJson('/api/v1/auth/login', [
            'email'    => $user->email,
            'password' => 'password',
        ])->assertOk()
          ->assertJsonStructure(['data', 'token', 'message']);
    });

    it('retourne 401 avec des identifiants invalides', function () {
        $this->postJson('/api/v1/auth/login', [
            'email'    => 'inconnu@example.com',
            'password' => 'mauvais',
        ])->assertUnauthorized();
    });

    it('retourne 422 si les champs sont absents', function () {
        $this->postJson('/api/v1/auth/login', [])
            ->assertUnprocessable()
            ->assertJsonStructure(['message', 'errors']);
    });
});

describe('POST /api/v1/auth/logout', function () {
    it('déconnecte un utilisateur authentifié', function () {
        Sanctum::actingAs(User::factory()->create());

        $this->postJson('/api/v1/auth/logout')
            ->assertOk()
            ->assertJsonPath('message', 'Déconnexion réussie.');
    });

    it('retourne 401 sans token', function () {
        $this->postJson('/api/v1/auth/logout')
            ->assertUnauthorized();
    });
});

describe('GET /api/v1/auth/me', function () {
    it('retourne le profil de l\'utilisateur connecté', function () {
        Sanctum::actingAs($user = User::factory()->create());

        $this->getJson('/api/v1/auth/me')
            ->assertOk()
            ->assertJsonPath('id', $user->id)
            ->assertJsonMissing(['password']);
    });

    it('retourne 401 sans token', function () {
        $this->getJson('/api/v1/auth/me')
            ->assertUnauthorized();
    });
});
