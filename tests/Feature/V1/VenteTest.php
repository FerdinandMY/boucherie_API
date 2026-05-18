<?php

declare(strict_types=1);

use App\Models\Boucherie;
use App\Models\EnumValeur;
use App\Models\LigneVente;
use App\Models\Produit;
use App\Models\Stock;
use App\Models\Vente;
use Laravel\Sanctum\Sanctum;

// Helper : crée les prérequis pour une vente valide
function prepareVenteData(Boucherie $boucherie, array $overrides = []): array
{
    EnumValeur::firstOrCreate(
        ['type' => 'type_vente', 'valeur' => 'comptoir'],
        ['libelle' => 'Comptoir', 'systeme' => true]
    );

    $produit = Produit::factory()->create([
        'boucherie_id'  => $boucherie->id,
        'prix_unitaire' => 2500,
    ]);

    Stock::factory()->create([
        'boucherie_id' => $boucherie->id,
        'produit_id'   => $produit->id,
        'quantite'     => 100,
    ]);

    return array_merge([
        'type_vente' => 'comptoir',
        'lignes'     => [
            [
                'produit_id'    => $produit->id,
                'quantite'      => 5,
                'prix_unitaire' => 2500,
            ],
        ],
    ], $overrides);
}

describe('GET /api/v1/ventes', function () {
    it('retourne la liste des ventes (boucher)', function () {
        $boucherie = Boucherie::factory()->create();
        $boucher   = boucherUser($boucherie);
        Sanctum::actingAs($boucher);

        Vente::factory()->count(2)->create([
            'boucherie_id' => $boucherie->id,
            'user_id'      => $boucher->id,
        ]);

        $this->getJson('/api/v1/ventes')
            ->assertOk()
            ->assertJsonStructure(['data', 'links', 'meta']);
    });

    it('filtre par statut', function () {
        $boucherie = Boucherie::factory()->create();
        $boucher   = boucherUser($boucherie);
        Sanctum::actingAs($boucher);

        Vente::factory()->create(['boucherie_id' => $boucherie->id, 'user_id' => $boucher->id, 'statut' => 'en_cours']);
        Vente::factory()->create(['boucherie_id' => $boucherie->id, 'user_id' => $boucher->id, 'statut' => 'annulee']);

        $response = $this->getJson('/api/v1/ventes?statut=en_cours')
            ->assertOk();

        $data = $response->json('data');
        expect(collect($data)->every(fn ($v) => $v['statut'] === 'en_cours'))->toBeTrue();
    });

    it('retourne 403 pour un fournisseur', function () {
        Sanctum::actingAs(fournisseurUser());

        $this->getJson('/api/v1/ventes')
            ->assertForbidden();
    });

    it('retourne 401 sans authentification', function () {
        $this->getJson('/api/v1/ventes')
            ->assertUnauthorized();
    });
});

describe('POST /api/v1/ventes', function () {
    it('crée une vente et décrémente le stock (boucher)', function () {
        $boucherie = Boucherie::factory()->create();
        $boucher   = boucherUser($boucherie);
        Sanctum::actingAs($boucher);

        $data  = prepareVenteData($boucherie);
        $stock = Stock::where('boucherie_id', $boucherie->id)->first();
        $qteInitiale = (float) $stock->quantite;

        $this->postJson('/api/v1/ventes', $data)
            ->assertCreated()
            ->assertJsonPath('message', 'Vente créée avec succès.')
            ->assertJsonStructure(['data' => ['lignes']]);

        $this->assertDatabaseHas('stocks', [
            'id'       => $stock->id,
            'quantite' => $qteInitiale - 5,
        ]);
    });

    it('retourne 422 si le stock est insuffisant', function () {
        $boucherie = Boucherie::factory()->create();
        $boucher   = boucherUser($boucherie);
        Sanctum::actingAs($boucher);

        EnumValeur::firstOrCreate(
            ['type' => 'type_vente', 'valeur' => 'comptoir'],
            ['libelle' => 'Comptoir', 'systeme' => true]
        );

        $produit = Produit::factory()->create(['boucherie_id' => $boucherie->id]);
        Stock::factory()->create([
            'boucherie_id' => $boucherie->id,
            'produit_id'   => $produit->id,
            'quantite'     => 2,
        ]);

        $this->postJson('/api/v1/ventes', [
            'type_vente' => 'comptoir',
            'lignes'     => [
                ['produit_id' => $produit->id, 'quantite' => 100, 'prix_unitaire' => 2500],
            ],
        ])->assertUnprocessable();
    });

    it('retourne 422 si type_vente est invalide', function () {
        $boucherie = Boucherie::factory()->create();
        Sanctum::actingAs(boucherUser($boucherie));

        $this->postJson('/api/v1/ventes', [
            'type_vente' => 'type_inexistant',
            'lignes'     => [['produit_id' => '00000000-0000-0000-0000-000000000000', 'quantite' => 1]],
        ])->assertUnprocessable();
    });

    it('retourne 422 si lignes est absent', function () {
        $boucherie = Boucherie::factory()->create();
        Sanctum::actingAs(boucherUser($boucherie));

        $this->postJson('/api/v1/ventes', ['type_vente' => 'comptoir'])
            ->assertUnprocessable();
    });

    it('retourne 401 sans authentification', function () {
        $this->postJson('/api/v1/ventes', [])
            ->assertUnauthorized();
    });
});

describe('GET /api/v1/ventes/{id}', function () {
    it('retourne le détail d\'une vente (boucher propriétaire)', function () {
        $boucherie = Boucherie::factory()->create();
        $boucher   = boucherUser($boucherie);
        $vente     = Vente::factory()->create([
            'boucherie_id' => $boucherie->id,
            'user_id'      => $boucher->id,
        ]);
        Sanctum::actingAs($boucher);

        $this->getJson("/api/v1/ventes/{$vente->id}")
            ->assertOk()
            ->assertJsonPath('data.id', $vente->id);
    });

    it('retourne 404 si introuvable', function () {
        $boucherie = Boucherie::factory()->create();
        Sanctum::actingAs(boucherUser($boucherie));

        $this->getJson('/api/v1/ventes/00000000-0000-0000-0000-000000000000')
            ->assertNotFound();
    });
});

describe('PATCH /api/v1/ventes/{id}/statut', function () {
    it('met à jour le statut d\'une vente (boucher)', function () {
        $boucherie = Boucherie::factory()->create();
        $boucher   = boucherUser($boucherie);
        $vente     = Vente::factory()->create([
            'boucherie_id' => $boucherie->id,
            'user_id'      => $boucher->id,
            'statut'       => 'en_cours',
        ]);
        Sanctum::actingAs($boucher);

        $this->patchJson("/api/v1/ventes/{$vente->id}/statut", ['statut' => 'validee'])
            ->assertOk()
            ->assertJsonPath('data.statut', 'validee')
            ->assertJsonPath('message', 'Statut mis à jour avec succès.');
    });

    it('annule la vente et restaure le stock', function () {
        $boucherie = Boucherie::factory()->create();
        $boucher   = boucherUser($boucherie);
        $produit   = Produit::factory()->create(['boucherie_id' => $boucherie->id]);
        $stock     = Stock::factory()->create([
            'boucherie_id' => $boucherie->id,
            'produit_id'   => $produit->id,
            'quantite'     => 50,
        ]);
        $vente = Vente::factory()->create([
            'boucherie_id' => $boucherie->id,
            'user_id'      => $boucher->id,
            'statut'       => 'en_cours',
        ]);
        LigneVente::create([
            'vente_id'      => $vente->id,
            'produit_id'    => $produit->id,
            'quantite'      => 10,
            'prix_unitaire' => 2500,
            'sous_total'    => 25000,
        ]);
        Sanctum::actingAs($boucher);

        $this->patchJson("/api/v1/ventes/{$vente->id}/statut", ['statut' => 'annulee'])
            ->assertOk()
            ->assertJsonPath('data.statut', 'annulee');

        $this->assertDatabaseHas('stocks', [
            'id'       => $stock->id,
            'quantite' => 60,
        ]);
    });

    it('retourne 422 si le statut est absent', function () {
        $boucherie = Boucherie::factory()->create();
        $boucher   = boucherUser($boucherie);
        $vente     = Vente::factory()->create([
            'boucherie_id' => $boucherie->id,
            'user_id'      => $boucher->id,
        ]);
        Sanctum::actingAs($boucher);

        $this->patchJson("/api/v1/ventes/{$vente->id}/statut", [])
            ->assertUnprocessable();
    });
});

describe('DELETE /api/v1/ventes/{id}', function () {
    it('supprime une vente (boucher)', function () {
        $boucherie = Boucherie::factory()->create();
        $boucher   = boucherUser($boucherie);
        $vente     = Vente::factory()->create([
            'boucherie_id' => $boucherie->id,
            'user_id'      => $boucher->id,
        ]);
        Sanctum::actingAs($boucher);

        $this->deleteJson("/api/v1/ventes/{$vente->id}")
            ->assertNoContent();

        $this->assertDatabaseMissing('ventes', ['id' => $vente->id]);
    });

    it('retourne 401 sans authentification', function () {
        $boucherie = Boucherie::factory()->create();
        $vente     = Vente::factory()->create(['boucherie_id' => $boucherie->id]);

        $this->deleteJson("/api/v1/ventes/{$vente->id}")
            ->assertUnauthorized();
    });
});
