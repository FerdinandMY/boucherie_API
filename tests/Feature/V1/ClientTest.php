<?php

declare(strict_types=1);

use App\Models\Boucherie;
use App\Models\Client;
use Laravel\Sanctum\Sanctum;

describe('GET /api/v1/clients', function () {
    it('retourne la liste des clients de la boucherie (boucher)', function () {
        $boucherie = Boucherie::factory()->create();
        Sanctum::actingAs(boucherUser($boucherie));
        Client::factory()->count(3)->create(['boucherie_id' => $boucherie->id]);

        $this->getJson('/api/v1/clients')
            ->assertOk()
            ->assertJsonStructure(['data', 'links', 'meta']);
    });

    it('retourne 403 pour un fournisseur', function () {
        Sanctum::actingAs(fournisseurUser());

        $this->getJson('/api/v1/clients')
            ->assertForbidden();
    });

    it('retourne 401 sans authentification', function () {
        $this->getJson('/api/v1/clients')
            ->assertUnauthorized();
    });
});

describe('POST /api/v1/clients', function () {
    it('crée un client (boucher)', function () {
        $boucherie = Boucherie::factory()->create();
        Sanctum::actingAs(boucherUser($boucherie));

        $this->postJson('/api/v1/clients', [
            'nom'       => 'Restaurant La Bonne Table',
            'telephone' => '+22607000000',
            'email'     => 'contact@labonnetable.com',
            'adresse'   => 'Quartier commercial',
        ])->assertCreated()
          ->assertJsonPath('data.nom', 'Restaurant La Bonne Table')
          ->assertJsonPath('message', 'Client créé avec succès.');
    });

    it('retourne 422 si le nom est absent', function () {
        $boucherie = Boucherie::factory()->create();
        Sanctum::actingAs(boucherUser($boucherie));

        $this->postJson('/api/v1/clients', [])
            ->assertUnprocessable()
            ->assertJsonStructure(['message', 'errors']);
    });

    it('retourne 403 pour un fournisseur', function () {
        Sanctum::actingAs(fournisseurUser());

        $this->postJson('/api/v1/clients', ['nom' => 'Test'])
            ->assertForbidden();
    });

    it('retourne 401 sans authentification', function () {
        $this->postJson('/api/v1/clients', ['nom' => 'Test'])
            ->assertUnauthorized();
    });
});

describe('GET /api/v1/clients/{id}', function () {
    it('retourne le détail d\'un client', function () {
        $boucherie = Boucherie::factory()->create();
        $client    = Client::factory()->create(['boucherie_id' => $boucherie->id]);
        Sanctum::actingAs(boucherUser($boucherie));

        $this->getJson("/api/v1/clients/{$client->id}")
            ->assertOk()
            ->assertJsonPath('data.id', $client->id)
            ->assertJsonPath('data.nom', $client->nom);
    });

    it('retourne 404 si introuvable', function () {
        $boucherie = Boucherie::factory()->create();
        Sanctum::actingAs(boucherUser($boucherie));

        $this->getJson('/api/v1/clients/00000000-0000-0000-0000-000000000000')
            ->assertNotFound();
    });
});

describe('PUT /api/v1/clients/{id}', function () {
    it('met à jour un client (boucher)', function () {
        $boucherie = Boucherie::factory()->create();
        $client    = Client::factory()->create(['boucherie_id' => $boucherie->id]);
        Sanctum::actingAs(boucherUser($boucherie));

        $this->putJson("/api/v1/clients/{$client->id}", ['nom' => 'Nouveau Nom'])
            ->assertOk()
            ->assertJsonPath('data.nom', 'Nouveau Nom')
            ->assertJsonPath('message', 'Client mis à jour avec succès.');
    });
});

describe('DELETE /api/v1/clients/{id}', function () {
    it('supprime un client (boucher)', function () {
        $boucherie = Boucherie::factory()->create();
        $client    = Client::factory()->create(['boucherie_id' => $boucherie->id]);
        Sanctum::actingAs(boucherUser($boucherie));

        $this->deleteJson("/api/v1/clients/{$client->id}")
            ->assertNoContent();

        $this->assertDatabaseMissing('clients', ['id' => $client->id]);
    });

    it('retourne 401 sans authentification', function () {
        $client = Client::factory()->create();

        $this->deleteJson("/api/v1/clients/{$client->id}")
            ->assertUnauthorized();
    });
});
