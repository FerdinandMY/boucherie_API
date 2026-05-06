<?php

declare(strict_types=1);

use App\Models\Typestock;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

describe('GET /api/v1/type-stocks', function () {
    it('retourne la liste des types de stock', function () {
        Sanctum::actingAs(User::factory()->create());
        Typestock::factory()->count(2)->create();

        $this->getJson('/api/v1/type-stocks')
            ->assertOk()
            ->assertJsonStructure(['data']);
    });

    it('retourne 401 sans authentification', function () {
        $this->getJson('/api/v1/type-stocks')
            ->assertUnauthorized();
    });
});

describe('POST /api/v1/type-stocks', function () {
    it('crée un type de stock avec des données valides', function () {
        Sanctum::actingAs(User::factory()->create());

        $this->postJson('/api/v1/type-stocks', [
            'type_name'   => 'Viande rouge',
            'description' => 'Bœuf, agneau, etc.',
        ])->assertCreated()
          ->assertJsonPath('data.type_name', 'Viande rouge')
          ->assertJsonPath('message', 'Type de stock créé avec succès.');
    });

    it('retourne 422 si type_name est absent', function () {
        Sanctum::actingAs(User::factory()->create());

        $this->postJson('/api/v1/type-stocks', [])
            ->assertUnprocessable()
            ->assertJsonStructure(['message', 'errors']);
    });

    it('retourne 401 sans authentification', function () {
        $this->postJson('/api/v1/type-stocks', [])
            ->assertUnauthorized();
    });
});

describe('GET /api/v1/type-stocks/{id}', function () {
    it('retourne le détail d\'un type de stock', function () {
        Sanctum::actingAs(User::factory()->create());
        $type = Typestock::factory()->create();

        $this->getJson("/api/v1/type-stocks/{$type->id}")
            ->assertOk()
            ->assertJsonPath('data.id', $type->id);
    });

    it('retourne 404 si le type n\'existe pas', function () {
        Sanctum::actingAs(User::factory()->create());

        $this->getJson('/api/v1/type-stocks/999')
            ->assertNotFound();
    });
});

describe('DELETE /api/v1/type-stocks/{id}', function () {
    it('supprime un type de stock', function () {
        Sanctum::actingAs(User::factory()->create());
        $type = Typestock::factory()->create();

        $this->deleteJson("/api/v1/type-stocks/{$type->id}")
            ->assertNoContent();
    });

    it('retourne 401 sans authentification', function () {
        $this->deleteJson('/api/v1/type-stocks/1')
            ->assertUnauthorized();
    });
});
