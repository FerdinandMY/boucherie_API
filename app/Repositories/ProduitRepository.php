<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Produit;
use Illuminate\Pagination\LengthAwarePaginator;

class ProduitRepository
{
    public function __construct(private readonly Produit $model) {}

    public function paginate(string $boucherieId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->query()
            ->where('boucherie_id', $boucherieId)
            ->select(['id', 'boucherie_id', 'nom', 'description', 'categorie', 'unite', 'prix_unitaire', 'created_at', 'updated_at'])
            ->latest()
            ->paginate($perPage);
    }

    public function findOrFail(string $id): Produit
    {
        return $this->model->query()->findOrFail($id);
    }

    public function create(array $data): Produit
    {
        return $this->model->query()->create($data);
    }

    public function update(string $id, array $data): Produit
    {
        $produit = $this->model->query()->findOrFail($id);
        $produit->update($data);
        return $produit;
    }

    public function delete(string $id): void
    {
        $this->model->query()->findOrFail($id)->delete();
    }
}
