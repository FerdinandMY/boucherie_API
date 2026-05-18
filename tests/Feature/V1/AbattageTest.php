<?php

declare(strict_types=1);

use App\Models\Abattage;
use App\Models\Animal;
use App\Models\Boucherie;
use App\Models\Produit;
use Laravel\Sanctum\Sanctum;

describe('GET /api/v1/abattages', function () {
    it('retourne la liste des abattages (boucher)', function () {
        $boucherie = Boucherie::factory()->create();
        $boucher   = boucherUser($boucherie);
        Sanctum::actingAs($boucher);

        Abattage::factory()->count(2)->create([
            'boucherie_id' => $boucherie->id,
            'user_id'      => $boucher->id,
        ]);

        $this->getJson('/api/v1/abattages')
            ->assertOk()
            ->assertJsonStructure(['data', 'links', 'meta']);
    });

    it('retourne 403 si non autorisé (rôle manquant)', function () {
        // Un utilisateur sans rôle ne peut pas accéder
        $user = \App\Models\User::factory()->create();
        Sanctum::actingAs($user);

        $this->getJson('/api/v1/abattages')
            ->assertForbidden();
    });

    it('retourne 401 sans authentification', function () {
        $this->getJson('/api/v1/abattages')
            ->assertUnauthorized();
    });
});

describe('POST /api/v1/abattages', function () {
    it('enregistre un abattage et crée le stock (boucher)', function () {
        $boucherie = Boucherie::factory()->create();
        $boucher   = boucherUser($boucherie);
        $animal    = Animal::factory()->create([
            'boucherie_id' => $boucherie->id,
            'statut'       => 'en_attente',
        ]);
        $produit = Produit::factory()->create(['boucherie_id' => $boucherie->id]);

        Sanctum::actingAs($boucher);

        $this->postJson('/api/v1/abattages', [
            'animal_id'         => $animal->id,
            'date_abattage'     => '2026-05-10',
            'poids_carcasse_kg' => 210.5,
            'rendement_pct'     => 60.0,
            'stocks'            => [
                [
                    'produit_id'   => $produit->id,
                    'quantite'     => 210.5,
                    'seuil_alerte' => 10,
                ],
            ],
        ])->assertCreated()
          ->assertJsonPath('message', 'Abattage enregistré avec succès.')
          ->assertJsonStructure(['data' => ['stocks']]);
    });

    it('retourne 422 si animal_id est absent', function () {
        $boucherie = Boucherie::factory()->create();
        Sanctum::actingAs(boucherUser($boucherie));

        $this->postJson('/api/v1/abattages', [])
            ->assertUnprocessable()
            ->assertJsonStructure(['message', 'errors']);
    });

    it('retourne 422 si stocks manque pour un boucher', function () {
        $boucherie = Boucherie::factory()->create();
        $boucher   = boucherUser($boucherie);
        $animal    = Animal::factory()->create([
            'boucherie_id' => $boucherie->id,
            'statut'       => 'en_attente',
        ]);
        Sanctum::actingAs($boucher);

        $this->postJson('/api/v1/abattages', [
            'animal_id'         => $animal->id,
            'date_abattage'     => '2026-05-10',
            'poids_carcasse_kg' => 200,
        ])->assertUnprocessable();
    });

    it('retourne 401 sans authentification', function () {
        $this->postJson('/api/v1/abattages', [])
            ->assertUnauthorized();
    });
});

describe('GET /api/v1/abattages/{id}', function () {
    it('retourne le détail d\'un abattage (boucher propriétaire)', function () {
        $boucherie = Boucherie::factory()->create();
        $boucher   = boucherUser($boucherie);
        $abattage  = Abattage::factory()->create([
            'boucherie_id' => $boucherie->id,
            'user_id'      => $boucher->id,
        ]);
        Sanctum::actingAs($boucher);

        $this->getJson("/api/v1/abattages/{$abattage->id}")
            ->assertOk()
            ->assertJsonPath('data.id', $abattage->id);
    });

    it('retourne 404 si introuvable', function () {
        $boucherie = Boucherie::factory()->create();
        Sanctum::actingAs(boucherUser($boucherie));

        $this->getJson('/api/v1/abattages/00000000-0000-0000-0000-000000000000')
            ->assertNotFound();
    });
});
