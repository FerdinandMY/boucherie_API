<?php

declare(strict_types=1);

use App\Models\Butcher;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

describe('GET /api/v1/butchers', function () {
    it('retourne la liste paginée des boucheries', function () {
        Sanctum::actingAs(User::factory()->create());
        Butcher::factory()->count(3)->create();

        $this->getJson('/api/v1/butchers')
            ->assertOk()
            ->assertJsonStructure(['data']);
    });

    it('retourne 401 sans authentification', function () {
        $this->getJson('/api/v1/butchers')
            ->assertUnauthorized();
    });
});

describe('POST /api/v1/butchers', function () {
    it('crée une boucherie avec des données valides', function () {
        Sanctum::actingAs(User::factory()->create());

        $this->postJson('/api/v1/butchers', [
            'name'        => 'Boucherie Centrale',
            'address'     => '12 rue de la Viande',
            'city'        => 'Paris',
            'postal_code' => '75001',
        ])->assertCreated()
          ->assertJsonPath('data.name', 'Boucherie Centrale')
          ->assertJsonPath('message', 'Boucherie créée avec succès.');
    });

    it('retourne 422 si les champs obligatoires manquent', function () {
        Sanctum::actingAs(User::factory()->create());

        $this->postJson('/api/v1/butchers', [])
            ->assertUnprocessable()
            ->assertJsonStructure(['message', 'errors']);
    });

    it('retourne 401 sans authentification', function () {
        $this->postJson('/api/v1/butchers', [])
            ->assertUnauthorized();
    });
});

describe('GET /api/v1/butchers/{id}', function () {
    it('retourne le détail d\'une boucherie', function () {
        Sanctum::actingAs(User::factory()->create());
        $butcher = Butcher::factory()->create();

        $this->getJson("/api/v1/butchers/{$butcher->id}")
            ->assertOk()
            ->assertJsonPath('data.id', $butcher->id);
    });

    it('retourne 404 si la boucherie n\'existe pas', function () {
        Sanctum::actingAs(User::factory()->create());

        $this->getJson('/api/v1/butchers/999')
            ->assertNotFound();
    });

    it('retourne 401 sans authentification', function () {
        $this->getJson('/api/v1/butchers/1')
            ->assertUnauthorized();
    });
});

describe('PUT /api/v1/butchers/{id}', function () {
    it('met à jour une boucherie', function () {
        Sanctum::actingAs(User::factory()->create());
        $butcher = Butcher::factory()->create();

        $this->putJson("/api/v1/butchers/{$butcher->id}", ['name' => 'Nouveau Nom'])
            ->assertOk()
            ->assertJsonPath('data.name', 'Nouveau Nom');
    });

    it('retourne 404 si la boucherie n\'existe pas', function () {
        Sanctum::actingAs(User::factory()->create());

        $this->putJson('/api/v1/butchers/999', ['name' => 'Test'])
            ->assertNotFound();
    });
});

describe('DELETE /api/v1/butchers/{id}', function () {
    it('supprime une boucherie', function () {
        Sanctum::actingAs(User::factory()->create());
        $butcher = Butcher::factory()->create();

        $this->deleteJson("/api/v1/butchers/{$butcher->id}")
            ->assertNoContent();
    });

    it('retourne 401 sans authentification', function () {
        $this->deleteJson('/api/v1/butchers/1')
            ->assertUnauthorized();
    });
});
