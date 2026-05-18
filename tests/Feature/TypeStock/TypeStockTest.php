<?php

declare(strict_types=1);

// L'API V1 n'expose pas de route /type-stocks.
// Les types de stock (StockCategorie) sont gérés via les EnumValeurs ou directement.

test('placeholder TypeStockTest', function () {
    expect(true)->toBeTrue();
})->skip('Route /type-stocks non présente dans la V1 — modèle Typestock obsolète.');
