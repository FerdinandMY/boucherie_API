<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Stock;
use Illuminate\Pagination\LengthAwarePaginator;

class StockRepository
{
    public function __construct(private readonly Stock $model) {}

    public function paginate(?string $boucherieId = null, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->query()
            ->when($boucherieId, fn ($q) => $q->where('boucherie_id', $boucherieId))
            ->with(['produit'])
            ->select(['id', 'boucherie_id', 'produit_id', 'abattage_id', 'quantite', 'seuil_alerte', 'updated_at'])
            ->paginate($perPage);
    }

    public function findOrFail(string $id): Stock
    {
        return $this->model->query()->with(['produit', 'abattage'])->findOrFail($id);
    }

    public function findByBoucherieAndProduit(string $boucherieId, string $produitId): ?Stock
    {
        return $this->model->query()
            ->where('boucherie_id', $boucherieId)
            ->where('produit_id', $produitId)
            ->first();
    }

    public function create(array $data): Stock
    {
        return $this->model->query()->create($data);
    }

    public function update(string $id, array $data): Stock
    {
        $stock = $this->model->query()->findOrFail($id);
        $stock->update($data);
        return $stock;
    }

    public function mouvements(string $id, int $perPage = 15): LengthAwarePaginator
    {
        $stock = $this->findOrFail($id);
        return $stock->mouvements()->with('user')->latest()->paginate($perPage);
    }
}
