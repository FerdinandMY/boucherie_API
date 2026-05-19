<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Distribution;
use App\Models\DistributionLigne;
use App\Repositories\DistributionRepository;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class DistributionService
{
    public function __construct(private readonly DistributionRepository $repository) {}

    public function paginateByFournisseur(int $fournisseurUserId): LengthAwarePaginator
    {
        return $this->repository->paginateByFournisseur($fournisseurUserId);
    }

    public function paginateByBoucherie(string $boucherieId): LengthAwarePaginator
    {
        return $this->repository->paginateByBoucherie($boucherieId);
    }

    public function paginateAll(): LengthAwarePaginator
    {
        return $this->repository->paginateAll();
    }

    public function findById(string $id): Distribution
    {
        return $this->repository->findOrFail($id);
    }

    public function create(array $data, int $fournisseurUserId): Distribution
    {
        return DB::transaction(function () use ($data, $fournisseurUserId) {
            $lignes = $data['lignes'] ?? [];
            unset($data['lignes']);

            $data['fournisseur_user_id'] = $fournisseurUserId;
            $data['statut']             = 'en_attente';

            $distribution = $this->repository->create($data);

            // v2 — lignes par catégorie (optionnel)
            foreach ($lignes as $ligne) {
                DistributionLigne::create([
                    'distribution_id' => $distribution->id,
                    'categorie'       => $ligne['categorie'],
                    'poids_kg'        => $ligne['poids_kg'],
                    'prix_par_kg'     => $ligne['prix_par_kg'] ?? null,
                ]);
            }

            return $distribution->fresh(['lignes', 'boucherie', 'abattage']);
        });
    }

    public function rejeter(string $id, int $fournisseurUserId): Distribution
    {
        $distribution = $this->repository->findOrFail($id);

        if ($distribution->fournisseur_user_id !== $fournisseurUserId) {
            abort(403, 'Vous ne pouvez pas modifier cette distribution.');
        }

        if ($distribution->statut !== 'en_attente') {
            throw ValidationException::withMessages([
                'statut' => ['Seule une distribution en attente peut être annulée.'],
            ]);
        }

        return $this->repository->update($id, ['statut' => 'rejetee']);
    }
}
