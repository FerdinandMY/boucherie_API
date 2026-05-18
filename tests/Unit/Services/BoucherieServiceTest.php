<?php

declare(strict_types=1);

use App\Models\Boucherie;
use App\Services\BoucherieService;
use Illuminate\Database\Eloquent\ModelNotFoundException;

describe('BoucherieService', function () {
    beforeEach(function () {
        $this->service = app(BoucherieService::class);
    });

    describe('paginate()', function () {
        it('retourne une liste paginée vide par défaut', function () {
            $result = $this->service->paginate();

            expect($result->total())->toBe(0)
                ->and($result->items())->toBeEmpty();
        });

        it('retourne toutes les boucheries créées', function () {
            Boucherie::factory()->count(5)->create();

            $result = $this->service->paginate(15);

            expect($result->total())->toBe(5);
        });

        it('respecte la pagination', function () {
            Boucherie::factory()->count(10)->create();

            $page1 = $this->service->paginate(3);

            expect($page1->perPage())->toBe(3)
                ->and($page1->count())->toBe(3);
        });
    });

    describe('findById()', function () {
        it('retourne la boucherie correspondante', function () {
            $boucherie = Boucherie::factory()->create(['nom' => 'Boucherie Test']);

            $found = $this->service->findById($boucherie->id);

            expect($found->id)->toBe($boucherie->id)
                ->and($found->nom)->toBe('Boucherie Test');
        });

        it('lève ModelNotFoundException pour un id inexistant', function () {
            expect(fn () => $this->service->findById('00000000-0000-0000-0000-000000000000'))
                ->toThrow(ModelNotFoundException::class);
        });
    });

    describe('create()', function () {
        it('crée une boucherie avec les données fournies', function () {
            $boucherie = $this->service->create([
                'nom'     => 'Boucherie Centrale',
                'adresse' => '10 rue du Marché',
                'ville'   => 'Ouagadougou',
            ]);

            expect($boucherie)->toBeInstanceOf(Boucherie::class)
                ->and($boucherie->nom)->toBe('Boucherie Centrale')
                ->and($boucherie->ville)->toBe('Ouagadougou');

            $this->assertDatabaseHas('boucheries', ['nom' => 'Boucherie Centrale']);
        });

        it('définit actif = true par défaut', function () {
            $boucherie = $this->service->create([
                'nom'   => 'Test',
                'adresse' => 'Adresse',
                'ville'   => 'Ville',
            ]);

            expect($boucherie->actif)->toBeTrue();
        });
    });

    describe('update()', function () {
        it('met à jour les champs fournis', function () {
            $boucherie = Boucherie::factory()->create(['nom' => 'Ancien Nom', 'ville' => 'Ancienne Ville']);

            $updated = $this->service->update($boucherie->id, [
                'nom'   => 'Nouveau Nom',
                'ville' => 'Nouvelle Ville',
            ]);

            expect($updated->nom)->toBe('Nouveau Nom')
                ->and($updated->ville)->toBe('Nouvelle Ville');

            $this->assertDatabaseHas('boucheries', [
                'id'  => $boucherie->id,
                'nom' => 'Nouveau Nom',
            ]);
        });

        it('lève ModelNotFoundException si la boucherie est introuvable', function () {
            expect(fn () => $this->service->update('00000000-0000-0000-0000-000000000000', ['nom' => 'X']))
                ->toThrow(ModelNotFoundException::class);
        });
    });

    describe('delete()', function () {
        it('supprime la boucherie de la base de données', function () {
            $boucherie = Boucherie::factory()->create();

            $this->service->delete($boucherie->id);

            $this->assertDatabaseMissing('boucheries', ['id' => $boucherie->id]);
        });

        it('lève ModelNotFoundException si la boucherie est introuvable', function () {
            expect(fn () => $this->service->delete('00000000-0000-0000-0000-000000000000'))
                ->toThrow(ModelNotFoundException::class);
        });
    });
});
