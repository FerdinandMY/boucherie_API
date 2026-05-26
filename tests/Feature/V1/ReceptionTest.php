<?php

declare(strict_types=1);

use App\Models\Abattage;
use App\Models\Boucherie;
use App\Models\Distribution;
use App\Models\Fournisseur;
use App\Models\Produit;
use App\Models\Reception;
use Laravel\Sanctum\Sanctum;

describe('GET /api/v1/receptions', function () {
    it('retourne les réceptions de la boucherie (boucher)', function () {
        $boucherie = Boucherie::factory()->create();
        Sanctum::actingAs(boucherUser($boucherie));

        Reception::factory()->count(2)->create(['boucherie_id' => $boucherie->id]);

        $this->getJson('/api/v1/receptions')
            ->assertOk()
            ->assertJsonStructure(['data', 'links', 'meta']);
    });

    it('retourne les réceptions du fournisseur connecté', function () {
        $fournisseur = Fournisseur::factory()->create();
        $user        = fournisseurUser($fournisseur);
        Sanctum::actingAs($user);

        $this->getJson('/api/v1/receptions')
            ->assertOk()
            ->assertJsonStructure(['data']);
    });

    it('retourne 401 sans authentification', function () {
        $this->getJson('/api/v1/receptions')
            ->assertUnauthorized();
    });
});

describe('POST /api/v1/receptions', function () {
    it('réceptionne une distribution et met à jour le stock (boucher)', function () {
        $boucherie   = Boucherie::factory()->create();
        $boucher     = boucherUser($boucherie);
        $fournisseur = Fournisseur::factory()->create();
        $fourn_user  = fournisseurUser($fournisseur);

        $produit     = Produit::factory()->create(['boucherie_id' => $boucherie->id]);
        $abattage    = Abattage::factory()->create([
            'boucherie_id' => $boucherie->id,
            'user_id'      => $fourn_user->id,
        ]);
        $distribution = Distribution::factory()->create([
            'boucherie_id'        => $boucherie->id,
            'fournisseur_user_id' => $fourn_user->id,
            'produit_id'          => $produit->id,
            'abattage_id'         => $abattage->id,
            'statut'              => 'en_attente',
        ]);

        Sanctum::actingAs($boucher);

        $this->postJson('/api/v1/receptions', [
            'distribution_id' => $distribution->id,
            'quantite_recue'  => 40.5,
            'date_reception'  => '2026-05-26',
        ])->assertCreated()
          ->assertJsonPath('message', 'Réception enregistrée. Stock mis à jour.');
    });

    it('retourne 422 si la distribution est déjà réceptionnée', function () {
        $boucherie   = Boucherie::factory()->create();
        $boucher     = boucherUser($boucherie);
        $distribution = Distribution::factory()->create([
            'boucherie_id' => $boucherie->id,
            'statut'       => 'acceptee',
        ]);
        Sanctum::actingAs($boucher);

        $this->postJson('/api/v1/receptions', [
            'distribution_id' => $distribution->id,
            'quantite_recue'  => 10.0,
            'date_reception'  => '2026-05-26',
        ])->assertUnprocessable();
    });

    it('retourne 422 si les champs obligatoires sont absents', function () {
        $boucherie = Boucherie::factory()->create();
        Sanctum::actingAs(boucherUser($boucherie));

        $this->postJson('/api/v1/receptions', [])
            ->assertUnprocessable()
            ->assertJsonStructure(['message', 'errors']);
    });

    it('retourne 401 sans authentification', function () {
        $this->postJson('/api/v1/receptions', [])
            ->assertUnauthorized();
    });
});

describe('GET /api/v1/receptions/{id}', function () {
    it('retourne le détail d\'une réception (boucher propriétaire)', function () {
        $boucherie = Boucherie::factory()->create();
        $reception = Reception::factory()->create(['boucherie_id' => $boucherie->id]);
        Sanctum::actingAs(boucherUser($boucherie));

        $this->getJson("/api/v1/receptions/{$reception->id}")
            ->assertOk()
            ->assertJsonPath('data.id', $reception->id);
    });

    it('retourne 403 si la réception appartient à une autre boucherie', function () {
        $boucherie1 = Boucherie::factory()->create();
        $boucherie2 = Boucherie::factory()->create();
        $reception  = Reception::factory()->create(['boucherie_id' => $boucherie2->id]);
        Sanctum::actingAs(boucherUser($boucherie1));

        $this->getJson("/api/v1/receptions/{$reception->id}")
            ->assertForbidden();
    });

    it('retourne 404 si introuvable', function () {
        $boucherie = Boucherie::factory()->create();
        Sanctum::actingAs(boucherUser($boucherie));

        $this->getJson('/api/v1/receptions/00000000-0000-0000-0000-000000000000')
            ->assertNotFound();
    });
});
