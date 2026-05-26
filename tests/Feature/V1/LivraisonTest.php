<?php

declare(strict_types=1);

use App\Models\Boucherie;
use App\Models\EnumValeur;
use App\Models\Vente;
use Laravel\Sanctum\Sanctum;

beforeEach(function () {
    EnumValeur::firstOrCreate(['type' => 'statut_livraison', 'valeur' => 'en_attente'], ['libelle' => 'En attente']);
    EnumValeur::firstOrCreate(['type' => 'statut_livraison', 'valeur' => 'en_cours'],   ['libelle' => 'En cours']);
    EnumValeur::firstOrCreate(['type' => 'statut_livraison', 'valeur' => 'livree'],     ['libelle' => 'Livrée']);
});

describe('POST /api/v1/ventes/{id}/livraison', function () {
    it('crée une livraison pour une vente (boucher)', function () {
        $boucherie = Boucherie::factory()->create();
        $vente     = Vente::factory()->create(['boucherie_id' => $boucherie->id]);
        Sanctum::actingAs(boucherUser($boucherie));

        $this->postJson("/api/v1/ventes/{$vente->id}/livraison", [
            'adresse_livraison' => '12 rue de la Paix, Abidjan',
            'date_prevue'       => '2026-06-01',
        ])->assertCreated()
          ->assertJsonPath('message', 'Livraison créée avec succès.')
          ->assertJsonPath('data.adresse_livraison', '12 rue de la Paix, Abidjan');
    });

    it('retourne 422 si adresse_livraison est absente', function () {
        $boucherie = Boucherie::factory()->create();
        $vente     = Vente::factory()->create(['boucherie_id' => $boucherie->id]);
        Sanctum::actingAs(boucherUser($boucherie));

        $this->postJson("/api/v1/ventes/{$vente->id}/livraison", [])
            ->assertUnprocessable()
            ->assertJsonStructure(['errors' => ['adresse_livraison']]);
    });

    it('retourne 403 pour un fournisseur', function () {
        $vente = Vente::factory()->create();
        Sanctum::actingAs(fournisseurUser());

        $this->postJson("/api/v1/ventes/{$vente->id}/livraison", [
            'adresse_livraison' => '12 rue de la Paix',
        ])->assertForbidden();
    });

    it('retourne 401 sans authentification', function () {
        $vente = Vente::factory()->create();

        $this->postJson("/api/v1/ventes/{$vente->id}/livraison", [])
            ->assertUnauthorized();
    });
});

describe('PATCH /api/v1/ventes/{id}/livraison', function () {
    it('met à jour le statut d\'une livraison (boucher)', function () {
        $boucherie = Boucherie::factory()->create();
        $boucher   = boucherUser($boucherie);
        $vente     = Vente::factory()->create(['boucherie_id' => $boucherie->id]);
        Sanctum::actingAs($boucher);

        // Créer la livraison d'abord
        $this->postJson("/api/v1/ventes/{$vente->id}/livraison", [
            'adresse_livraison' => '12 rue de la Paix, Abidjan',
        ]);

        $this->patchJson("/api/v1/ventes/{$vente->id}/livraison", [
            'statut' => 'en_cours',
        ])->assertOk()
          ->assertJsonPath('message', 'Livraison mise à jour avec succès.')
          ->assertJsonPath('data.statut', 'en_cours');
    });

    it('retourne 401 sans authentification', function () {
        $vente = Vente::factory()->create();

        $this->patchJson("/api/v1/ventes/{$vente->id}/livraison", [])
            ->assertUnauthorized();
    });
});
