<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Livraison;

class LivraisonRepository
{
    public function __construct(private readonly Livraison $model) {}

    public function findByVente(string $venteId): ?Livraison
    {
        return $this->model->query()->where('vente_id', $venteId)->first();
    }

    public function findOrFail(string $id): Livraison
    {
        return $this->model->query()->findOrFail($id);
    }

    public function create(array $data): Livraison
    {
        return $this->model->query()->create($data);
    }

    public function update(string $venteId, array $data): Livraison
    {
        $livraison = $this->model->query()->where('vente_id', $venteId)->firstOrFail();
        $livraison->update($data);
        return $livraison;
    }
}
