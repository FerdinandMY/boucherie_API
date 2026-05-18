<?php

declare(strict_types=1);

use App\Models\Boucherie;
use App\Models\EnumValeur;
use App\Models\MouvementStock;
use App\Models\Produit;
use App\Models\Stock;
use App\Models\User;
use App\Models\Vente;
use App\Services\VenteService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

describe('VenteService', function () {
    beforeEach(function () {
        $this->service   = app(VenteService::class);
        $this->boucherie = Boucherie::factory()->create();
        $this->user      = User::factory()->create(['boucherie_id' => $this->boucherie->id]);

        EnumValeur::firstOrCreate(
            ['type' => 'type_vente', 'valeur' => 'comptoir'],
            ['libelle' => 'Comptoir', 'systeme' => true]
        );
    });

    describe('paginate()', function () {
        it('retourne une liste paginée vide par défaut', function () {
            $result = $this->service->paginate($this->boucherie->id);

            expect($result->total())->toBe(0);
        });

        it('filtre par boucherie_id', function () {
            $autreBoucherie = Boucherie::factory()->create();
            $autreUser      = User::factory()->create(['boucherie_id' => $autreBoucherie->id]);

            Vente::factory()->create(['boucherie_id' => $this->boucherie->id, 'user_id' => $this->user->id]);
            Vente::factory()->create(['boucherie_id' => $autreBoucherie->id, 'user_id' => $autreUser->id]);

            $result = $this->service->paginate($this->boucherie->id);

            expect($result->total())->toBe(1);
        });

        it('filtre par statut', function () {
            Vente::factory()->create(['boucherie_id' => $this->boucherie->id, 'user_id' => $this->user->id, 'statut' => 'en_cours']);
            Vente::factory()->create(['boucherie_id' => $this->boucherie->id, 'user_id' => $this->user->id, 'statut' => 'annulee']);

            $result = $this->service->paginate($this->boucherie->id, ['statut' => 'en_cours']);

            expect($result->total())->toBe(1)
                ->and($result->items()[0]->statut)->toBe('en_cours');
        });
    });

    describe('create()', function () {
        it('crée une vente, décrémente le stock et crée un mouvement', function () {
            $produit = Produit::factory()->create(['boucherie_id' => $this->boucherie->id, 'prix_unitaire' => 3000]);
            $stock   = Stock::factory()->create([
                'boucherie_id' => $this->boucherie->id,
                'produit_id'   => $produit->id,
                'quantite'     => 100,
            ]);

            $vente = $this->service->create([
                'boucherie_id' => $this->boucherie->id,
                'type_vente'   => 'comptoir',
                'lignes'       => [
                    ['produit_id' => $produit->id, 'quantite' => 10, 'prix_unitaire' => 3000],
                ],
            ], $this->user->id);

            expect($vente)->toBeInstanceOf(Vente::class)
                ->and($vente->montant_total)->toBe('30000.00')
                ->and($vente->statut)->toBe('en_cours');

            $this->assertDatabaseHas('stocks', ['id' => $stock->id, 'quantite' => 90]);
            $this->assertDatabaseHas('mouvements_stock', [
                'stock_id' => $stock->id,
                'type'     => 'sortie',
                'quantite' => 10,
            ]);
        });

        it('lève une ValidationException si le stock est insuffisant', function () {
            $produit = Produit::factory()->create(['boucherie_id' => $this->boucherie->id]);
            Stock::factory()->create([
                'boucherie_id' => $this->boucherie->id,
                'produit_id'   => $produit->id,
                'quantite'     => 2,
            ]);

            expect(fn () => $this->service->create([
                'boucherie_id' => $this->boucherie->id,
                'type_vente'   => 'comptoir',
                'lignes'       => [
                    ['produit_id' => $produit->id, 'quantite' => 50],
                ],
            ], $this->user->id))->toThrow(ValidationException::class);
        });

        it('lève une ValidationException si le produit est introuvable', function () {
            expect(fn () => $this->service->create([
                'boucherie_id' => $this->boucherie->id,
                'type_vente'   => 'comptoir',
                'lignes'       => [
                    ['produit_id' => '00000000-0000-0000-0000-000000000000', 'quantite' => 1],
                ],
            ], $this->user->id))->toThrow(ValidationException::class);
        });

        it('lève une ValidationException pour vente livraison sans client', function () {
            expect(fn () => $this->service->create([
                'boucherie_id' => $this->boucherie->id,
                'type_vente'   => 'livraison',
                'lignes'       => [],
            ], $this->user->id))->toThrow(ValidationException::class);
        });
    });

    describe('updateStatut()', function () {
        it('met à jour le statut d\'une vente', function () {
            $vente = Vente::factory()->create([
                'boucherie_id' => $this->boucherie->id,
                'user_id'      => $this->user->id,
                'statut'       => 'en_cours',
            ]);

            $updated = $this->service->updateStatut($vente->id, ['statut' => 'validee'], $this->user->id);

            expect($updated->statut)->toBe('validee');
        });

        it('annule une vente et restitue le stock', function () {
            $produit = Produit::factory()->create(['boucherie_id' => $this->boucherie->id]);
            $stock   = Stock::factory()->create([
                'boucherie_id' => $this->boucherie->id,
                'produit_id'   => $produit->id,
                'quantite'     => 30,
            ]);
            $vente = Vente::factory()->create([
                'boucherie_id' => $this->boucherie->id,
                'user_id'      => $this->user->id,
                'statut'       => 'en_cours',
            ]);
            \App\Models\LigneVente::create([
                'vente_id'      => $vente->id,
                'produit_id'    => $produit->id,
                'quantite'      => 10,
                'prix_unitaire' => 2000,
                'sous_total'    => 20000,
            ]);

            $this->service->updateStatut($vente->id, ['statut' => 'annulee'], $this->user->id);

            $this->assertDatabaseHas('stocks', ['id' => $stock->id, 'quantite' => 40]);
            $this->assertDatabaseHas('mouvements_stock', [
                'stock_id' => $stock->id,
                'type'     => 'entree',
                'quantite' => 10,
            ]);
        });

        it('lève une ValidationException si la vente ne peut plus être annulée', function () {
            $vente = Vente::factory()->create([
                'boucherie_id' => $this->boucherie->id,
                'user_id'      => $this->user->id,
                'statut'       => 'annulee',
            ]);

            expect(fn () => $this->service->updateStatut($vente->id, ['statut' => 'annulee'], $this->user->id))
                ->toThrow(ValidationException::class);
        });
    });

    describe('findById()', function () {
        it('retourne la vente correspondante', function () {
            $vente = Vente::factory()->create([
                'boucherie_id' => $this->boucherie->id,
                'user_id'      => $this->user->id,
            ]);

            $found = $this->service->findById($vente->id);

            expect($found->id)->toBe($vente->id);
        });

        it('lève ModelNotFoundException pour un id inexistant', function () {
            expect(fn () => $this->service->findById('00000000-0000-0000-0000-000000000000'))
                ->toThrow(ModelNotFoundException::class);
        });
    });

    describe('delete()', function () {
        it('supprime une vente', function () {
            $vente = Vente::factory()->create([
                'boucherie_id' => $this->boucherie->id,
                'user_id'      => $this->user->id,
            ]);

            $this->service->delete($vente->id);

            $this->assertDatabaseMissing('ventes', ['id' => $vente->id]);
        });
    });
});
