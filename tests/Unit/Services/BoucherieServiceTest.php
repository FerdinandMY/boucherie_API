<?php

declare(strict_types=1);

use App\Models\Boucherie;
use App\Services\BoucherieService;
use Illuminate\Database\Eloquent\ModelNotFoundException;

beforeEach(function () {
    $this->service = app(BoucherieService::class);
});

// ── paginate() ──────────────────────────────────────────────────────────────

it('BoucherieService::paginate() retourne une liste paginée vide par défaut', function () {
    $result = $this->service->paginate();

    expect($result->total())->toBe(0)
        ->and($result->items())->toBeEmpty();
});

it('BoucherieService::paginate() retourne toutes les boucheries créées', function () {
    Boucherie::factory()->count(5)->create();

    $result = $this->service->paginate(15);

    expect($result->total())->toBe(5);
});

it('BoucherieService::paginate() respecte la pagination', function () {
    Boucherie::factory()->count(10)->create();

    $page1 = $this->service->paginate(3);

    expect($page1->perPage())->toBe(3)
        ->and($page1->count())->toBe(3);
});

// ── findById() ──────────────────────────────────────────────────────────────

it('BoucherieService::findById() retourne la boucherie correspondante', function () {
    $boucherie = Boucherie::factory()->create(['nom' => 'Boucherie Test']);

    $found = $this->service->findById($boucherie->id);

    expect($found->id)->toBe($boucherie->id)
        ->and($found->nom)->toBe('Boucherie Test');
});

it('BoucherieService::findById() lève ModelNotFoundException pour un id inexistant', function () {
    expect(fn () => $this->service->findById('00000000-0000-0000-0000-000000000000'))
        ->toThrow(ModelNotFoundException::class);
});

// ── create() ────────────────────────────────────────────────────────────────

it('BoucherieService::create() crée une boucherie avec les données fournies', function () {
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

it('BoucherieService::create() définit actif = true par défaut', function () {
    $boucherie = $this->service->create([
        'nom'     => 'Test',
        'adresse' => 'Adresse',
        'ville'   => 'Ville',
    ]);

    $this->assertDatabaseHas('boucheries', ['nom' => 'Test', 'actif' => 1]);
});

// ── update() ────────────────────────────────────────────────────────────────

it('BoucherieService::update() met à jour les champs fournis', function () {
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

it('BoucherieService::update() lève ModelNotFoundException si boucherie introuvable', function () {
    expect(fn () => $this->service->update('00000000-0000-0000-0000-000000000000', ['nom' => 'X']))
        ->toThrow(ModelNotFoundException::class);
});

// ── delete() ────────────────────────────────────────────────────────────────

it('BoucherieService::delete() supprime la boucherie de la base de données', function () {
    $boucherie = Boucherie::factory()->create();

    $this->service->delete($boucherie->id);

    $this->assertDatabaseMissing('boucheries', ['id' => $boucherie->id]);
});

it('BoucherieService::delete() lève ModelNotFoundException si boucherie introuvable', function () {
    expect(fn () => $this->service->delete('00000000-0000-0000-0000-000000000000'))
        ->toThrow(ModelNotFoundException::class);
});
