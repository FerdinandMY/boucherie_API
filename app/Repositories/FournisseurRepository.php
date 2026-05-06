<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Fournisseur;
use Illuminate\Pagination\LengthAwarePaginator;

class FournisseurRepository
{
    public function __construct(private readonly Fournisseur $model) {}

    public function paginate(string $boucherieId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->query()
            ->where('boucherie_id', $boucherieId)
            ->select(['id', 'boucherie_id', 'nom', 'contact', 'telephone', 'email', 'adresse', 'created_at', 'updated_at'])
            ->latest()
            ->paginate($perPage);
    }

    public function findOrFail(string $id): Fournisseur
    {
        return $this->model->query()->with('achatsFournisseurs')->findOrFail($id);
    }

    public function create(array $data): Fournisseur
    {
        return $this->model->query()->create($data);
    }

    public function update(string $id, array $data): Fournisseur
    {
        $fournisseur = $this->model->query()->findOrFail($id);
        $fournisseur->update($data);
        return $fournisseur;
    }

    public function delete(string $id): void
    {
        $this->model->query()->findOrFail($id)->delete();
    }
}
