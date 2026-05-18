<?php

declare(strict_types=1);

use App\Models\AchatFournisseur;
use App\Models\Boucherie;
use App\Models\EnumValeur;
use App\Models\Fournisseur;
use Laravel\Sanctum\Sanctum;

describe('GET /api/v1/achats-fournisseurs', function () {
    it('retourne la liste des achats (boucher)', function () {
        $boucherie   = Boucherie::factory()->create();
        $boucher     = boucherUser($boucherie);
        Sanctum::actingAs($boucher);

        AchatFournisseur::factory()->count(2)->create([
            'boucherie_id' => $boucherie->id,
            'user_id'      => $boucher->id,
        ]);

        $this->getJson('/api/v1/achats-fournisseurs')
            ->assertOk()
            ->assertJsonStructure(['data', 'links', 'meta']);
    });

    it('retourne 401 sans authentification', function () {
        $this->getJson('/api/v1/achats-fournisseurs')
            ->assertUnauthorized();
    });
});

describe('POST /api/v1/achats-fournisseurs', function () {
    it('crée un achat avec les animaux associés (boucher)', function () {
        $boucherie   = Boucherie::factory()->create();
        $boucher     = boucherUser($boucherie);
        $fournisseur = Fournisseur::factory()->create(['boucherie_id' => $boucherie->id]);

        EnumValeur::factory()->especeAnimal('bovin')->create();

        Sanctum::actingAs($boucher);

        $this->postJson('/api/v1/achats-fournisseurs', [
            'fournisseur_id' => $fournisseur->id,
            'date_achat'     => '2026-05-01',
            'montant_total'  => 540000,
            'animaux'        => [
                [
                    'espece'       => 'bovin',
                    'poids_vif_kg' => 350,
                    'prix_achat'   => 270000,
                    'numero_tag'   => 'TAG-001',
                ],
            ],
        ])->assertCreated()
          ->assertJsonPath('message', 'Achat enregistré avec succès.')
          ->assertJsonStructure(['data' => ['animaux']]);
    });

    it('crée un achat (fournisseur depuis son profil)', function () {
        $fournisseur = Fournisseur::factory()->create();
        $user        = fournisseurUser($fournisseur);
        Sanctum::actingAs($user);

        EnumValeur::factory()->especeAnimal('bovin')->create();

        $this->postJson('/api/v1/achats-fournisseurs', [
            'date_achat'    => '2026-05-01',
            'montant_total' => 200000,
            'animaux'       => [
                [
                    'espece'       => 'bovin',
                    'poids_vif_kg' => 300,
                    'prix_achat'   => 200000,
                ],
            ],
        ])->assertCreated();
    });

    it('retourne 422 si fournisseur_id manque (boucher)', function () {
        $boucherie = Boucherie::factory()->create();
        Sanctum::actingAs(boucherUser($boucherie));

        $this->postJson('/api/v1/achats-fournisseurs', [
            'date_achat'    => '2026-05-01',
            'montant_total' => 100000,
            'animaux'       => [['espece' => 'bovin', 'poids_vif_kg' => 200, 'prix_achat' => 100000]],
        ])->assertUnprocessable();
    });

    it('retourne 422 si les champs obligatoires sont absents', function () {
        $boucherie = Boucherie::factory()->create();
        Sanctum::actingAs(boucherUser($boucherie));

        $this->postJson('/api/v1/achats-fournisseurs', [])
            ->assertUnprocessable()
            ->assertJsonStructure(['message', 'errors']);
    });

    it('retourne 401 sans authentification', function () {
        $this->postJson('/api/v1/achats-fournisseurs', [])
            ->assertUnauthorized();
    });
});

describe('GET /api/v1/achats-fournisseurs/{id}', function () {
    it('retourne le détail d\'un achat (boucher propriétaire)', function () {
        $boucherie   = Boucherie::factory()->create();
        $boucher     = boucherUser($boucherie);
        $achat       = AchatFournisseur::factory()->create([
            'boucherie_id' => $boucherie->id,
            'user_id'      => $boucher->id,
        ]);
        Sanctum::actingAs($boucher);

        $this->getJson("/api/v1/achats-fournisseurs/{$achat->id}")
            ->assertOk()
            ->assertJsonPath('data.id', $achat->id);
    });

    it('retourne 404 si introuvable', function () {
        $boucherie = Boucherie::factory()->create();
        Sanctum::actingAs(boucherUser($boucherie));

        $this->getJson('/api/v1/achats-fournisseurs/00000000-0000-0000-0000-000000000000')
            ->assertNotFound();
    });
});
