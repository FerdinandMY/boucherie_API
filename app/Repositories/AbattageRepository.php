<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Abattage;
use Illuminate\Pagination\LengthAwarePaginator;

class AbattageRepository
{
    public function __construct(private readonly Abattage $model) {}

    public function paginate(string $boucherieId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->query()
            ->where('boucherie_id', $boucherieId)
            ->with(['animal', 'user'])
            ->select(['id', 'animal_id', 'user_id', 'boucherie_id', 'date_abattage',
                      'poids_carcasse_kg', 'rendement_pct', 'notes', 'created_at', 'updated_at'])
            ->latest()
            ->paginate($perPage);
    }

    public function findOrFail(string $id): Abattage
    {
        return $this->model->query()->with(['animal', 'user', 'stocks.mouvements'])->findOrFail($id);
    }

    public function create(array $data): Abattage
    {
        return $this->model->query()->create($data);
    }
}
