<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Paiement;
use App\Models\Vente;
use App\Repositories\PaiementRepository;
use App\Repositories\VenteRepository;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Validation\ValidationException;

class PaiementService
{
    public function __construct(
        private readonly PaiementRepository $repository,
        private readonly VenteRepository    $venteRepository
    ) {}

    public function paginateByVente(string $venteId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->paginateByVente($venteId, $perPage);
    }

    public function create(string $venteId, array $data, int $userId): Paiement
    {
        $vente  = $this->venteRepository->findOrFail($venteId);
        $somme  = $this->repository->sommeByVente($venteId);
        $nouveau = (float) $data['montant'];

        if ($somme + $nouveau > (float) $vente->montant_total) {
            throw ValidationException::withMessages([
                'montant' => ['Le montant dépasse le total de la vente.'],
            ]);
        }

        return $this->repository->create([
            'vente_id'       => $venteId,
            'user_id'        => $userId,
            'mode_paiement'  => $data['mode_paiement'],
            'montant'        => $nouveau,
            'date_paiement'  => $data['date_paiement'] ?? now(),
        ]);
    }
}
