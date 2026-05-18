<?php

declare(strict_types=1);

use App\Models\Boucherie;
use App\Models\Produit;
use App\Models\Stock;
use Laravel\Sanctum\Sanctum;

describe('GET /api/v1/stocks', function () {
    it('retourne la liste des stocks de la boucherie (boucher)', function () {
        $boucherie = Boucherie::factory()->create();
        $produit   = Produit::factory()->create(['boucherie_id' => $boucherie->id]);
        Sanctum::actingAs(boucherUser($boucherie));

        Stock::factory()->count(3)->create([
            'boucherie_id' => $boucherie->id,
            'produit_id'   => $produit->id,
        ]);

        $this->getJson('/api/v1/stocks')
            ->assertOk()
            ->assertJsonStructure(['data', 'links', 'meta']);
    });

    it('retourne 403 pour un fournisseur', function () {
        Sanctum::actingAs(fournisseurUser());

        $this->getJson('/api/v1/stocks')
            ->assertForbidden();
    });

    it('retourne 401 sans authentification', function () {
        $this->getJson('/api/v1/stocks')
            ->assertUnauthorized();
    });
});

describe('GET /api/v1/stocks/{id}', function () {
    it('retourne le détail d\'un stock (boucher propriétaire)', function () {
        $boucherie = Boucherie::factory()->create();
        $produit   = Produit::factory()->create(['boucherie_id' => $boucherie->id]);
        $stock     = Stock::factory()->create([
            'boucherie_id' => $boucherie->id,
            'produit_id'   => $produit->id,
        ]);
        Sanctum::actingAs(boucherUser($boucherie));

        $this->getJson("/api/v1/stocks/{$stock->id}")
            ->assertOk()
            ->assertJsonPath('data.id', $stock->id);
    });

    it('retourne 404 si introuvable', function () {
        $boucherie = Boucherie::factory()->create();
        Sanctum::actingAs(boucherUser($boucherie));

        $this->getJson('/api/v1/stocks/00000000-0000-0000-0000-000000000000')
            ->assertNotFound();
    });
});

describe('GET /api/v1/stocks/{id}/mouvements', function () {
    it('retourne les mouvements d\'un stock (boucher)', function () {
        $boucherie = Boucherie::factory()->create();
        $produit   = Produit::factory()->create(['boucherie_id' => $boucherie->id]);
        $stock     = Stock::factory()->create([
            'boucherie_id' => $boucherie->id,
            'produit_id'   => $produit->id,
        ]);
        Sanctum::actingAs(boucherUser($boucherie));

        $this->getJson("/api/v1/stocks/{$stock->id}/mouvements")
            ->assertOk()
            ->assertJsonStructure(['data']);
    });
});

describe('POST /api/v1/stocks/{id}/ajuster', function () {
    it('ajuste la quantité d\'un stock (boucher)', function () {
        $boucherie = Boucherie::factory()->create();
        $boucher   = boucherUser($boucherie);
        $produit   = Produit::factory()->create(['boucherie_id' => $boucherie->id]);
        $stock     = Stock::factory()->create([
            'boucherie_id' => $boucherie->id,
            'produit_id'   => $produit->id,
            'quantite'     => 50,
        ]);
        Sanctum::actingAs($boucher);

        $this->postJson("/api/v1/stocks/{$stock->id}/ajuster", [
            'quantite' => 10,
            'type'     => 'entree',
            'motif'    => 'Ajustement de test',
        ])->assertOk()
          ->assertJsonPath('message', 'Stock ajusté avec succès.');
    });

    it('retourne 422 si quantite est absente', function () {
        $boucherie = Boucherie::factory()->create();
        $produit   = Produit::factory()->create(['boucherie_id' => $boucherie->id]);
        $stock     = Stock::factory()->create([
            'boucherie_id' => $boucherie->id,
            'produit_id'   => $produit->id,
        ]);
        Sanctum::actingAs(boucherUser($boucherie));

        $this->postJson("/api/v1/stocks/{$stock->id}/ajuster", [])
            ->assertUnprocessable();
    });

    it('retourne 401 sans authentification', function () {
        $boucherie = Boucherie::factory()->create();
        $produit   = Produit::factory()->create(['boucherie_id' => $boucherie->id]);
        $stock     = Stock::factory()->create([
            'boucherie_id' => $boucherie->id,
            'produit_id'   => $produit->id,
        ]);

        $this->postJson("/api/v1/stocks/{$stock->id}/ajuster", ['quantite' => 5])
            ->assertUnauthorized();
    });
});
