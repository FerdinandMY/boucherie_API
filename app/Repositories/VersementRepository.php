<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Versement;
use Illuminate\Pagination\LengthAwarePaginator;

class VersementRepository
{
    public function __construct(private readonly Versement $model) {}

    public function paginateByBoucherie(string $boucherieId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->query()
            ->where('boucherie_id', $boucherieId)
            ->with(['fournisseurUser', 'validePar'])
            ->latest()
            ->paginate($perPage);
    }

    public function paginateByFournisseur(int $fournisseurUserId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->query()
            ->where('fournisseur_user_id', $fournisseurUserId)
            ->with(['boucherie', 'validePar'])
            ->latest()
            ->paginate($perPage);
    }

    public function paginateAll(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->query()
            ->with(['boucherie', 'fournisseurUser', 'validePar'])
            ->latest()
            ->paginate($perPage);
    }

    public function findOrFail(string $id): Versement
    {
        return $this->model->query()
            ->with(['boucherie', 'fournisseurUser', 'validePar', 'attachments'])
            ->findOrFail($id);
    }

    public function create(array $data): Versement
    {
        return $this->model->query()->create($data);
    }

    public function update(string $id, array $data): Versement
    {
        $versement = $this->findOrFail($id);
        $versement->update($data);
        return $versement->fresh(['boucherie', 'fournisseurUser', 'validePar']);
    }
}
