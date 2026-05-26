<?php

declare(strict_types=1);

use App\Models\Boucherie;
use App\Models\EnumValeur;
use App\Models\Vente;
use Laravel\Sanctum\Sanctum;

beforeEach(function () {
    EnumValeur::firstOrCreate(['type' => 'mode_paiement', 'valeur' => 'especes'],       ['libelle' => 'Espèces']);
    EnumValeur::firstOrCreate(['type' => 'mode_paiement', 'valeur' => 'mobile_money'],  ['libelle' => 'Mobile Money']);
});

describe('GET /api/v1/ventes/{id}/paiements', function () {
    it('retourne la liste des paiements d\'une vente (boucher)', function () {
        $boucherie = Boucherie::factory()->create();
        $boucher   = boucherUser($boucherie);
        $vente     = Vente::factory()->create(['boucherie_id' => $boucherie->id]);
        Sanctum::actingAs($boucher);

        $this->getJson("/api/v1/ventes/{$vente->id}/paiements")
            ->assertOk()
            ->assertJsonStructure(['data', 'links', 'meta']);
    });

    it('retourne 401 sans authentification', function () {
        $vente = Vente::factory()->create();

        $this->getJson("/api/v1/ventes/{$vente->id}/paiements")
            ->assertUnauthorized();
    });
});

describe('POST /api/v1/ventes/{id}/paiements', function () {
    it('enregistre un paiement (boucher)', function () {
        $boucherie = Boucherie::factory()->create();
        $boucher   = boucherUser($boucherie);
        $vente     = Vente::factory()->create([
            'boucherie_id'  => $boucherie->id,
            'montant_total' => 50000,
        ]);
        Sanctum::actingAs($boucher);

        $this->postJson("/api/v1/ventes/{$vente->id}/paiements", [
            'montant'       => 25000,
            'mode_paiement' => 'especes',
            'date_paiement' => '2026-05-20',
        ])->assertCreated()
          ->assertJsonPath('message', 'Paiement enregistré avec succès.')
          ->assertJsonPath('data.montant', '25000.00');
    });

    it('retourne 422 si le montant dépasse le total de la vente', function () {
        $boucherie = Boucherie::factory()->create();
        $vente     = Vente::factory()->create([
            'boucherie_id'  => $boucherie->id,
            'montant_total' => 10000,
        ]);
        Sanctum::actingAs(boucherUser($boucherie));

        $this->postJson("/api/v1/ventes/{$vente->id}/paiements", [
            'montant'       => 99999,
            'mode_paiement' => 'especes',
        ])->assertUnprocessable()
          ->assertJsonPath('errors.montant.0', 'Le montant dépasse le total de la vente.');
    });

    it('retourne 422 si mode_paiement est invalide', function () {
        $boucherie = Boucherie::factory()->create();
        $vente     = Vente::factory()->create(['boucherie_id' => $boucherie->id]);
        Sanctum::actingAs(boucherUser($boucherie));

        $this->postJson("/api/v1/ventes/{$vente->id}/paiements", [
            'montant'       => 1000,
            'mode_paiement' => 'bitcoin',
        ])->assertUnprocessable()
          ->assertJsonStructure(['errors' => ['mode_paiement']]);
    });

    it('retourne 422 si les champs obligatoires sont absents', function () {
        $boucherie = Boucherie::factory()->create();
        $vente     = Vente::factory()->create(['boucherie_id' => $boucherie->id]);
        Sanctum::actingAs(boucherUser($boucherie));

        $this->postJson("/api/v1/ventes/{$vente->id}/paiements", [])
            ->assertUnprocessable()
            ->assertJsonStructure(['message', 'errors']);
    });

    it('retourne 401 sans authentification', function () {
        $vente = Vente::factory()->create();

        $this->postJson("/api/v1/ventes/{$vente->id}/paiements", [])
            ->assertUnauthorized();
    });
});
