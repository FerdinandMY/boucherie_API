<?php

declare(strict_types=1);

use App\Models\Boucherie;
use App\Models\Fournisseur;
use Laravel\Sanctum\Sanctum;

describe('GET /api/v1/fournisseurs', function () {
    it('retourne la liste paginée (boucher)', function () {
        $boucherie = Boucherie::factory()->create();
        Sanctum::actingAs(boucherUser($boucherie));
        Fournisseur::factory()->count(2)->create(['boucherie_id' => $boucherie->id]);

        $this->getJson('/api/v1/fournisseurs')
            ->assertOk()
            ->assertJsonStructure(['data', 'links', 'meta']);
    });

    it('retourne 403 pour un fournisseur', function () {
        Sanctum::actingAs(fournisseurUser());

        $this->getJson('/api/v1/fournisseurs')
            ->assertForbidden();
    });

    it('retourne 401 sans authentification', function () {
        $this->getJson('/api/v1/fournisseurs')
            ->assertUnauthorized();
    });
});

describe('POST /api/v1/fournisseurs', function () {
    it('crée un fournisseur (boucher)', function () {
        $boucherie = Boucherie::factory()->create();
        Sanctum::actingAs(boucherUser($boucherie));

        $this->postJson('/api/v1/fournisseurs', [
            'nom'       => 'Élevage Koné',
            'contact'   => 'Mamadou Koné',
            'telephone' => '+22600000001',
            'email'     => 'mamadou@elevage.com',
            'adresse'   => 'Route de l\'élevage',
        ])->assertCreated()
          ->assertJsonPath('data.nom', 'Élevage Koné')
          ->assertJsonPath('message', 'Fournisseur créé avec succès.');
    });

    it('crée un fournisseur (admin)', function () {
        $boucherie = Boucherie::factory()->create();
        Sanctum::actingAs(adminUser($boucherie->id));

        $this->postJson('/api/v1/fournisseurs', ['nom' => 'Test Fournisseur'])
            ->assertCreated();
    });

    it('retourne 422 si le nom est absent', function () {
        $boucherie = Boucherie::factory()->create();
        Sanctum::actingAs(boucherUser($boucherie));

        $this->postJson('/api/v1/fournisseurs', [])
            ->assertUnprocessable()
            ->assertJsonStructure(['message', 'errors']);
    });

    it('retourne 403 pour un fournisseur', function () {
        Sanctum::actingAs(fournisseurUser());

        $this->postJson('/api/v1/fournisseurs', ['nom' => 'Test'])
            ->assertForbidden();
    });

    it('retourne 401 sans authentification', function () {
        $this->postJson('/api/v1/fournisseurs', ['nom' => 'Test'])
            ->assertUnauthorized();
    });
});

describe('GET /api/v1/fournisseurs/{id}', function () {
    it('retourne le détail d\'un fournisseur (boucher propriétaire)', function () {
        $boucherie   = Boucherie::factory()->create();
        $fournisseur = Fournisseur::factory()->create(['boucherie_id' => $boucherie->id]);
        Sanctum::actingAs(boucherUser($boucherie));

        $this->getJson("/api/v1/fournisseurs/{$fournisseur->id}")
            ->assertOk()
            ->assertJsonPath('data.id', $fournisseur->id);
    });

    it('retourne 404 si introuvable', function () {
        $boucherie = Boucherie::factory()->create();
        Sanctum::actingAs(boucherUser($boucherie));

        $this->getJson('/api/v1/fournisseurs/00000000-0000-0000-0000-000000000000')
            ->assertNotFound();
    });
});

describe('PUT /api/v1/fournisseurs/{id}', function () {
    it('met à jour un fournisseur (boucher)', function () {
        $boucherie   = Boucherie::factory()->create();
        $fournisseur = Fournisseur::factory()->create(['boucherie_id' => $boucherie->id]);
        Sanctum::actingAs(boucherUser($boucherie));

        $this->putJson("/api/v1/fournisseurs/{$fournisseur->id}", ['nom' => 'Nouveau Nom'])
            ->assertOk()
            ->assertJsonPath('data.nom', 'Nouveau Nom')
            ->assertJsonPath('message', 'Fournisseur mis à jour avec succès.');
    });

    it('retourne 404 si introuvable', function () {
        $boucherie = Boucherie::factory()->create();
        Sanctum::actingAs(boucherUser($boucherie));

        $this->putJson('/api/v1/fournisseurs/00000000-0000-0000-0000-000000000000', ['nom' => 'Test'])
            ->assertNotFound();
    });
});

describe('DELETE /api/v1/fournisseurs/{id}', function () {
    it('supprime un fournisseur (boucher)', function () {
        $boucherie   = Boucherie::factory()->create();
        $fournisseur = Fournisseur::factory()->create(['boucherie_id' => $boucherie->id]);
        Sanctum::actingAs(boucherUser($boucherie));

        $this->deleteJson("/api/v1/fournisseurs/{$fournisseur->id}")
            ->assertNoContent();

        $this->assertDatabaseMissing('fournisseurs', ['id' => $fournisseur->id]);
    });

    it('retourne 401 sans authentification', function () {
        $fournisseur = Fournisseur::factory()->create();

        $this->deleteJson("/api/v1/fournisseurs/{$fournisseur->id}")
            ->assertUnauthorized();
    });
});
