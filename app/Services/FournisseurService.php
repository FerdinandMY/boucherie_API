<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Fournisseur;
use App\Repositories\FournisseurRepository;
use Illuminate\Pagination\LengthAwarePaginator;

class FournisseurService
{
    public function __construct(private readonly FournisseurRepository $repository) {}

    public function paginate(string $boucherieId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->paginate($boucherieId, $perPage);
    }

    public function findById(string $id): Fournisseur
    {
        return $this->repository->findOrFail($id);
    }

    public function create(array $data, string $boucherieId): Fournisseur
    {
        return $this->repository->create(array_merge($data, ['boucherie_id' => $boucherieId]));
    }

    public function update(string $id, array $data): Fournisseur
    {
        return $this->repository->update($id, $data);
    }

    public function delete(string $id): void
    {
        $this->repository->delete($id);
    }
}
