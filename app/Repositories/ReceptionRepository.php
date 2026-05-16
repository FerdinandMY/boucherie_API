<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Reception;
use Illuminate\Pagination\LengthAwarePaginator;

class ReceptionRepository
{
    public function __construct(private readonly Reception $model) {}

    public function paginateByBoucherie(?string $boucherieId = null, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->query()
            ->when($boucherieId, fn ($q) => $q->where('boucherie_id', $boucherieId))
            ->with(['distribution.produit', 'distribution.fournisseurUser', 'user'])
            ->latest()
            ->paginate($perPage);
    }

    public function paginateByFournisseur(int $fournisseurUserId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->query()
            ->whereHas('distribution', fn ($q) => $q->where('fournisseur_user_id', $fournisseurUserId))
            ->with(['distribution.produit', 'distribution.fournisseurUser', 'user'])
            ->latest()
            ->paginate($perPage);
    }

    public function findOrFail(string $id): Reception
    {
        return $this->model->query()
            ->with(['distribution.produit', 'distribution.abattage', 'boucherie', 'user'])
            ->findOrFail($id);
    }

    public function create(array $data): Reception
    {
        return $this->model->query()->create($data);
    }
}
