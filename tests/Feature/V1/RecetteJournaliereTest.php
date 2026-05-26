<?php

declare(strict_types=1);

use App\Models\Boucherie;
use App\Models\EnumValeur;
use App\Models\Fournisseur;
use App\Models\RecetteJournaliere;
use Laravel\Sanctum\Sanctum;

beforeEach(function () {
    EnumValeur::firstOrCreate(['type' => 'categorie_produit', 'valeur' => 'viande_rouge'], ['libelle' => 'Viande rouge']);
});

describe('GET /api/v1/recettes', function () {
    it('retourne les recettes de la boucherie (boucher)', function () {
        $boucherie   = Boucherie::factory()->create();
        $fournisseur = Fournisseur::factory()->create();
        Sanctum::actingAs(boucherUser($boucherie));

        RecetteJournaliere::factory()->count(2)->create([
            'boucherie_id'   => $boucherie->id,
            'fournisseur_id' => $fournisseur->id,
        ]);

        $this->getJson('/api/v1/recettes')
            ->assertOk()
            ->assertJsonStructure(['data', 'links', 'meta']);
    });

    it('retourne les recettes du fournisseur connecté', function () {
        $fournisseur = Fournisseur::factory()->create();
        $user        = fournisseurUser($fournisseur);
        Sanctum::actingAs($user);

        $this->getJson('/api/v1/recettes')
            ->assertOk();
    });

    it('retourne 401 sans authentification', function () {
        $this->getJson('/api/v1/recettes')
            ->assertUnauthorized();
    });
});

describe('POST /api/v1/recettes', function () {
    it('crée une recette journalière (boucher avec fournisseur_id explicite)', function () {
        $boucherie   = Boucherie::factory()->create();
        $boucher     = boucherUser($boucherie);
        $fournisseur = Fournisseur::factory()->create();
        Sanctum::actingAs($boucher);

        $this->postJson('/api/v1/recettes', [
            'date'           => '2026-05-26',
            'montant_verse'  => 50000,
            'fournisseur_id' => $fournisseur->id,
            'lignes'         => [
                [
                    'categorie'      => 'viande_rouge',
                    'poids_kg_vendu' => 30.0,
                    'prix_par_kg'    => 2500,
                ],
            ],
        ])->assertCreated()
          ->assertJsonPath('message', 'Recette enregistrée avec succès.')
          ->assertJsonPath('data.statut_versement', 'en_attente');
    });

    it('retourne 422 si lignes est absent', function () {
        $boucherie   = Boucherie::factory()->create();
        $fournisseur = Fournisseur::factory()->create();
        Sanctum::actingAs(boucherUser($boucherie));

        $this->postJson('/api/v1/recettes', [
            'date'           => '2026-05-26',
            'montant_verse'  => 0,
            'fournisseur_id' => $fournisseur->id,
        ])->assertUnprocessable()
          ->assertJsonStructure(['errors' => ['lignes']]);
    });

    it('retourne 422 si fournisseur_id est absent et non résolvable', function () {
        $boucherie = Boucherie::factory()->create();
        Sanctum::actingAs(boucherUser($boucherie));

        $this->postJson('/api/v1/recettes', [
            'date'          => '2026-05-26',
            'montant_verse' => 0,
            'lignes'        => [
                ['categorie' => 'viande_rouge', 'poids_kg_vendu' => 10, 'prix_par_kg' => 2000],
            ],
        ])->assertStatus(422);
    });

    it('retourne 403 pour un fournisseur', function () {
        $fournisseur = Fournisseur::factory()->create();
        Sanctum::actingAs(fournisseurUser($fournisseur));

        $this->postJson('/api/v1/recettes', [
            'date'          => '2026-05-26',
            'montant_verse' => 0,
            'lignes'        => [],
        ])->assertForbidden();
    });

    it('retourne 401 sans authentification', function () {
        $this->postJson('/api/v1/recettes', [])
            ->assertUnauthorized();
    });
});

describe('GET /api/v1/recettes/{id}', function () {
    it('retourne le détail d\'une recette (boucher)', function () {
        $boucherie   = Boucherie::factory()->create();
        $fournisseur = Fournisseur::factory()->create();
        $recette     = RecetteJournaliere::factory()->create([
            'boucherie_id'   => $boucherie->id,
            'fournisseur_id' => $fournisseur->id,
        ]);
        Sanctum::actingAs(boucherUser($boucherie));

        $this->getJson("/api/v1/recettes/{$recette->id}")
            ->assertOk()
            ->assertJsonPath('data.id', $recette->id);
    });

    it('retourne 404 si introuvable', function () {
        $boucherie = Boucherie::factory()->create();
        Sanctum::actingAs(boucherUser($boucherie));

        $this->getJson('/api/v1/recettes/00000000-0000-0000-0000-000000000000')
            ->assertNotFound();
    });
});

describe('PATCH /api/v1/recettes/{id}/valider', function () {
    it('valide le versement d\'une recette (fournisseur propriétaire)', function () {
        $fournisseur = Fournisseur::factory()->create();
        $user        = fournisseurUser($fournisseur);
        $boucherie   = Boucherie::factory()->create();
        $recette     = RecetteJournaliere::factory()->create([
            'boucherie_id'     => $boucherie->id,
            'fournisseur_id'   => $fournisseur->id,
            'statut_versement' => 'en_attente',
        ]);
        Sanctum::actingAs($user);

        $this->patchJson("/api/v1/recettes/{$recette->id}/valider")
            ->assertOk()
            ->assertJsonPath('data.statut_versement', 'valide')
            ->assertJsonPath('message', 'Versement validé.');
    });
});

describe('PATCH /api/v1/recettes/{id}/rejeter', function () {
    it('rejette le versement d\'une recette (fournisseur propriétaire)', function () {
        $fournisseur = Fournisseur::factory()->create();
        $user        = fournisseurUser($fournisseur);
        $boucherie   = Boucherie::factory()->create();
        $recette     = RecetteJournaliere::factory()->create([
            'boucherie_id'     => $boucherie->id,
            'fournisseur_id'   => $fournisseur->id,
            'statut_versement' => 'en_attente',
        ]);
        Sanctum::actingAs($user);

        $this->patchJson("/api/v1/recettes/{$recette->id}/rejeter")
            ->assertOk()
            ->assertJsonPath('data.statut_versement', 'rejete')
            ->assertJsonPath('message', 'Versement rejeté.');
    });

    it('retourne 401 sans authentification', function () {
        $fournisseur = Fournisseur::factory()->create();
        $boucherie   = Boucherie::factory()->create();
        $recette     = RecetteJournaliere::factory()->create([
            'boucherie_id'   => $boucherie->id,
            'fournisseur_id' => $fournisseur->id,
        ]);

        $this->patchJson("/api/v1/recettes/{$recette->id}/rejeter")
            ->assertUnauthorized();
    });
});
