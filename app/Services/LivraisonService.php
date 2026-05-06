<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Livraison;
use App\Repositories\LivraisonRepository;
use App\Repositories\VenteRepository;

class LivraisonService
{
    public function __construct(
        private readonly LivraisonRepository $repository,
        private readonly VenteRepository     $venteRepository
    ) {}

    public function create(string $venteId, array $data, int $userId): Livraison
    {
        $this->venteRepository->findOrFail($venteId);

        return $this->repository->create([
            'vente_id'          => $venteId,
            'user_id'           => $userId,
            'adresse_livraison' => $data['adresse_livraison'],
            'statut'            => $data['statut'] ?? 'en_attente',
            'date_prevue'       => $data['date_prevue'] ?? null,
        ]);
    }

    public function update(string $venteId, array $data): Livraison
    {
        return $this->repository->update($venteId, $data);
    }
}
