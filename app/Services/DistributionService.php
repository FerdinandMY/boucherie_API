<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Distribution;
use App\Repositories\DistributionRepository;
use Illuminate\Pagination\LengthAwarePaginator;
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

    public function findById(string $id): Distribution
    {
        return $this->repository->findOrFail($id);
    }

    public function create(array $data, int $fournisseurUserId): Distribution
    {
        $data['fournisseur_user_id'] = $fournisseurUserId;
        $data['statut']             = 'en_attente';

        return $this->repository->create($data);
    }

    public function rejeter(string $id, int $fournisseurUserId): Distribution
    {
        $distribution = $this->repository->findOrFail($id);

        if ($distribution->fournisseur_user_id !== $fournisseurUserId) {
            throw ValidationException::withMessages([
                'distribution' => ['Vous ne pouvez pas modifier cette distribution.'],
            ]);
        }

        if ($distribution->statut !== 'en_attente') {
            throw ValidationException::withMessages([
                'statut' => ['Seule une distribution en attente peut être annulée.'],
            ]);
        }

        return $this->repository->update($id, ['statut' => 'rejetee']);
    }
}
