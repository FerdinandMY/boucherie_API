<?php

declare(strict_types=1);

use App\Models\Boucherie;
use App\Models\Produit;
use Laravel\Sanctum\Sanctum;

describe('GET /api/v1/produits', function () {
    it('retourne la liste des produits (boucher)', function () {
        $boucherie = Boucherie::factory()->create();
        Sanctum::actingAs(boucherUser($boucherie));
        Produit::factory()->count(3)->create(['boucherie_id' => $boucherie->id]);

        $this->getJson('/api/v1/produits')
            ->assertOk()
            ->assertJsonStructure(['data', 'links', 'meta']);
    });

    it('retourne la liste pour un fournisseur (lecture seule)', function () {
        Sanctum::actingAs(fournisseurUser());

        $this->getJson('/api/v1/produits')
            ->assertOk();
    });

    it('retourne 401 sans authentification', function () {
        $this->getJson('/api/v1/produits')
            ->assertUnauthorized();
    });
});

describe('POST /api/v1/produits', function () {
    it('crée un produit (boucher)', function () {
        $boucherie = Boucherie::factory()->create();
        Sanctum::actingAs(boucherUser($boucherie));

        $this->postJson('/api/v1/produits', [
            'nom'           => 'Côte de bœuf',
            'categorie'     => 'boeuf',
            'unite'         => 'kg',
            'prix_unitaire' => 2500,
            'description'   => 'Viande de qualité supérieure',
        ])->assertCreated()
          ->assertJsonPath('data.nom', 'Côte de bœuf')
          ->assertJsonPath('message', 'Produit créé avec succès.');
    });

    it('crée un produit (admin avec boucherie_id explicite)', function () {
        $boucherie = Boucherie::factory()->create();
        Sanctum::actingAs(adminUser());

        $this->postJson('/api/v1/produits', [
            'boucherie_id'  => $boucherie->id,
            'nom'           => 'Filet mignon',
            'categorie'     => 'boeuf',
            'unite'         => 'kg',
            'prix_unitaire' => 5000,
        ])->assertCreated();
    });

    it('retourne 422 si les champs obligatoires sont absents', function () {
        $boucherie = Boucherie::factory()->create();
        Sanctum::actingAs(boucherUser($boucherie));

        $this->postJson('/api/v1/produits', [])
            ->assertUnprocessable()
            ->assertJsonStructure(['message', 'errors']);
    });

    it('retourne 403 pour un fournisseur', function () {
        Sanctum::actingAs(fournisseurUser());

        $this->postJson('/api/v1/produits', ['nom' => 'Test'])
            ->assertForbidden();
    });

    it('retourne 401 sans authentification', function () {
        $this->postJson('/api/v1/produits', ['nom' => 'Test'])
            ->assertUnauthorized();
    });
});

describe('GET /api/v1/produits/{id}', function () {
    it('retourne le détail d\'un produit', function () {
        $boucherie = Boucherie::factory()->create();
        $produit   = Produit::factory()->create(['boucherie_id' => $boucherie->id]);
        Sanctum::actingAs(boucherUser($boucherie));

        $this->getJson("/api/v1/produits/{$produit->id}")
            ->assertOk()
            ->assertJsonPath('data.id', $produit->id)
            ->assertJsonPath('data.nom', $produit->nom);
    });

    it('retourne 404 si introuvable', function () {
        $boucherie = Boucherie::factory()->create();
        Sanctum::actingAs(boucherUser($boucherie));

        $this->getJson('/api/v1/produits/00000000-0000-0000-0000-000000000000')
            ->assertNotFound();
    });
});

describe('PUT /api/v1/produits/{id}', function () {
    it('met à jour un produit (boucher)', function () {
        $boucherie = Boucherie::factory()->create();
        $produit   = Produit::factory()->create(['boucherie_id' => $boucherie->id]);
        Sanctum::actingAs(boucherUser($boucherie));

        $this->putJson("/api/v1/produits/{$produit->id}", ['nom' => 'Nouveau Nom'])
            ->assertOk()
            ->assertJsonPath('data.nom', 'Nouveau Nom')
            ->assertJsonPath('message', 'Produit mis à jour avec succès.');
    });
});

describe('DELETE /api/v1/produits/{id}', function () {
    it('supprime un produit (boucher)', function () {
        $boucherie = Boucherie::factory()->create();
        $produit   = Produit::factory()->create(['boucherie_id' => $boucherie->id]);
        Sanctum::actingAs(boucherUser($boucherie));

        $this->deleteJson("/api/v1/produits/{$produit->id}")
            ->assertNoContent();

        $this->assertDatabaseMissing('produits', ['id' => $produit->id]);
    });

    it('retourne 401 sans authentification', function () {
        $produit = Produit::factory()->create();

        $this->deleteJson("/api/v1/produits/{$produit->id}")
            ->assertUnauthorized();
    });
});
