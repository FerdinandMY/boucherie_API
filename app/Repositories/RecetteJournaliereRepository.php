<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\RecetteJournaliere;
use Illuminate\Pagination\LengthAwarePaginator;

class RecetteJournaliereRepository
{
    public function __construct(private readonly RecetteJournaliere $model) {}

    public function paginateByBoucherie(string $boucherieId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->query()
            ->where('boucherie_id', $boucherieId)
            ->with('lignes')
            ->latest('date')
            ->paginate($perPage);
    }

    public function paginateByFournisseur(string $fournisseurId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->query()
            ->where('fournisseur_id', $fournisseurId)
            ->with(['lignes', 'boucherie'])
            ->latest('date')
            ->paginate($perPage);
    }

    public function paginateAll(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->query()
            ->with(['lignes', 'boucherie', 'fournisseur'])
            ->latest('date')
            ->paginate($perPage);
    }

    public function findOrFail(string $id): RecetteJournaliere
    {
        return $this->model->query()
            ->with(['lignes', 'boucherie', 'fournisseur'])
            ->findOrFail($id);
    }

    public function create(array $data): RecetteJournaliere
    {
        return $this->model->query()->create($data);
    }

    public function update(string $id, array $data): RecetteJournaliere
    {
        $recette = $this->model->query()->findOrFail($id);
        $recette->update($data);
        return $recette;
    }
}
