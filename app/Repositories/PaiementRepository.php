<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Paiement;
use App\Models\Vente;
use Illuminate\Pagination\LengthAwarePaginator;

class PaiementRepository
{
    public function __construct(private readonly Paiement $model) {}

    public function paginateByVente(string $venteId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->query()
            ->where('vente_id', $venteId)
            ->with('user')
            ->latest()
            ->paginate($perPage);
    }

    public function sommeByVente(string $venteId): float
    {
        return (float) $this->model->query()
            ->where('vente_id', $venteId)
            ->sum('montant');
    }

    public function create(array $data): Paiement
    {
        return $this->model->query()->create($data);
    }
}
