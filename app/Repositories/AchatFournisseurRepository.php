<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\AchatFournisseur;
use Illuminate\Pagination\LengthAwarePaginator;

class AchatFournisseurRepository
{
    public function __construct(private readonly AchatFournisseur $model) {}

    public function paginate(string $boucherieId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->query()
            ->where('boucherie_id', $boucherieId)
            ->with(['fournisseur', 'user'])
            ->select(['id', 'boucherie_id', 'fournisseur_id', 'user_id', 'reference', 'montant_total', 'date_achat', 'created_at', 'updated_at'])
            ->latest()
            ->paginate($perPage);
    }

    public function findOrFail(string $id): AchatFournisseur
    {
        return $this->model->query()->with(['fournisseur', 'animaux', 'user'])->findOrFail($id);
    }

    public function create(array $data): AchatFournisseur
    {
        return $this->model->query()->create($data);
    }
}
