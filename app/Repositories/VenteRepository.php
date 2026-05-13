<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Vente;
use Illuminate\Pagination\LengthAwarePaginator;

class VenteRepository
{
    public function __construct(private readonly Vente $model) {}

    public function paginate(?string $boucherieId = null, array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model->query()
            ->when($boucherieId, fn ($q) => $q->where('boucherie_id', $boucherieId))
            ->with(['client', 'user'])
            ->select(['id', 'boucherie_id', 'client_id', 'user_id', 'type_vente', 'statut', 'montant_total', 'created_at', 'updated_at']);

        if (isset($filters['statut'])) {
            $query->where('statut', $filters['statut']);
        }
        if (isset($filters['type_vente'])) {
            $query->where('type_vente', $filters['type_vente']);
        }
        if (isset($filters['date_debut'])) {
            $query->whereDate('created_at', '>=', $filters['date_debut']);
        }
        if (isset($filters['date_fin'])) {
            $query->whereDate('created_at', '<=', $filters['date_fin']);
        }

        return $query->latest()->paginate($perPage);
    }

    public function findOrFail(string $id): Vente
    {
        return $this->model->query()
            ->with(['client', 'user', 'lignes.produit', 'paiements', 'livraison'])
            ->findOrFail($id);
    }

    public function create(array $data): Vente
    {
        return $this->model->query()->create($data);
    }

    public function update(string $id, array $data): Vente
    {
        $vente = $this->model->query()->findOrFail($id);
        $vente->update($data);
        return $vente->fresh(['client', 'user', 'lignes.produit', 'paiements']);
    }
}
