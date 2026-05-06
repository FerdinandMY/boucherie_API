<?php

declare(strict_types=1);

use App\Models\Butcher;
use App\Models\Stock;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

describe('GET /api/v1/stocks', function () {
    it('retourne la liste paginée des stocks', function () {
        Sanctum::actingAs(User::factory()->create());

        $this->getJson('/api/v1/stocks')
            ->assertOk()
            ->assertJsonStructure(['data']);
    });

    it('retourne 401 sans authentification', function () {
        $this->getJson('/api/v1/stocks')
            ->assertUnauthorized();
    });
});

describe('POST /api/v1/stocks', function () {
    it('crée un stock avec des données valides', function () {
        Sanctum::actingAs(User::factory()->create());
        $butcher = Butcher::factory()->create();

        $this->postJson('/api/v1/stocks', [
            'butcher_id'     => $butcher->id,
            'product_name'   => 'Bœuf haché',
            'quantity'       => 50,
            'quantity_meat'  => 40,
            'quantity_tripe' => 10,
            'unit'           => 'kg',
            'date_added'     => '2026-05-01',
            'price_per_unit' => 12.50,
        ])->assertCreated()
          ->assertJsonPath('data.product_name', 'Bœuf haché')
          ->assertJsonPath('message', 'Stock créé avec succès.');
    });

    it('retourne 422 si les champs obligatoires manquent', function () {
        Sanctum::actingAs(User::factory()->create());

        $this->postJson('/api/v1/stocks', [])
            ->assertUnprocessable()
            ->assertJsonStructure(['message', 'errors']);
    });

    it('retourne 422 si butcher_id n\'existe pas', function () {
        Sanctum::actingAs(User::factory()->create());

        $this->postJson('/api/v1/stocks', [
            'butcher_id'     => 9999,
            'product_name'   => 'Test',
            'quantity'       => 10,
            'quantity_meat'  => 5,
            'quantity_tripe' => 5,
            'unit'           => 'kg',
            'date_added'     => '2026-05-01',
            'price_per_unit' => 5.00,
        ])->assertUnprocessable();
    });

    it('retourne 401 sans authentification', function () {
        $this->postJson('/api/v1/stocks', [])
            ->assertUnauthorized();
    });
});

describe('GET /api/v1/stocks/{id}', function () {
    it('retourne le détail d\'un stock', function () {
        Sanctum::actingAs(User::factory()->create());
        $butcher = Butcher::factory()->create();
        $stock   = Stock::factory()->create(['butcher_id' => $butcher->id]);

        $this->getJson("/api/v1/stocks/{$stock->id}")
            ->assertOk()
            ->assertJsonPath('data.id', $stock->id);
    });

    it('retourne 404 si le stock n\'existe pas', function () {
        Sanctum::actingAs(User::factory()->create());

        $this->getJson('/api/v1/stocks/999')
            ->assertNotFound();
    });
});

describe('DELETE /api/v1/stocks/{id}', function () {
    it('supprime un stock', function () {
        Sanctum::actingAs(User::factory()->create());
        $butcher = Butcher::factory()->create();
        $stock   = Stock::factory()->create(['butcher_id' => $butcher->id]);

        $this->deleteJson("/api/v1/stocks/{$stock->id}")
            ->assertNoContent();
    });

    it('retourne 401 sans authentification', function () {
        $this->deleteJson('/api/v1/stocks/1')
            ->assertUnauthorized();
    });
});
