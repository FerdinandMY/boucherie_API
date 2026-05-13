<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Produit;
use App\Repositories\ProduitRepository;
use Illuminate\Pagination\LengthAwarePaginator;

class ProduitService
{
    public function __construct(private readonly ProduitRepository $repository) {}

    public function paginate(?string $boucherieId = null, int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->paginate($boucherieId, $perPage);
    }

    public function findById(string $id): Produit
    {
        return $this->repository->findOrFail($id);
    }

    public function create(array $data, string $boucherieId): Produit
    {
        return $this->repository->create(array_merge($data, ['boucherie_id' => $boucherieId]));
    }

    public function update(string $id, array $data): Produit
    {
        return $this->repository->update($id, $data);
    }

    public function delete(string $id): void
    {
        $this->repository->delete($id);
    }
}
