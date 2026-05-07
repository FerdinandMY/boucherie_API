<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\MouvementStock;
use App\Models\Reception;
use App\Models\Stock;
use App\Repositories\DistributionRepository;
use App\Repositories\ReceptionRepository;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ReceptionService
{
    public function __construct(
        private readonly ReceptionRepository  $repository,
        private readonly DistributionRepository $distributionRepository,
    ) {}

    public function paginateByBoucherie(string $boucherieId): LengthAwarePaginator
    {
        return $this->repository->paginateByBoucherie($boucherieId);
    }

    public function findById(string $id): Reception
    {
        return $this->repository->findOrFail($id);
    }

    public function create(array $data, string $boucherieId, int $userId): Reception
    {
        return DB::transaction(function () use ($data, $boucherieId, $userId) {
            $distribution = $this->distributionRepository->findOrFail($data['distribution_id']);

            if ($distribution->boucherie_id !== $boucherieId) {
                throw ValidationException::withMessages([
                    'distribution_id' => ['Cette distribution ne vous est pas destinée.'],
                ]);
            }

            if ($distribution->statut !== 'en_attente') {
                throw ValidationException::withMessages([
                    'distribution_id' => ['Cette distribution a déjà été réceptionnée ou rejetée.'],
                ]);
            }

            $reception = $this->repository->create([
                'distribution_id' => $distribution->id,
                'boucherie_id'    => $boucherieId,
                'user_id'         => $userId,
                'quantite_recue'  => $data['quantite_recue'],
                'date_reception'  => $data['date_reception'],
                'notes'           => $data['notes'] ?? null,
            ]);

            // Marquer la distribution comme acceptée
            $this->distributionRepository->update($distribution->id, ['statut' => 'acceptee']);

            // Alimenter le stock
            $stock = Stock::firstOrCreate(
                ['boucherie_id' => $boucherieId, 'produit_id' => $distribution->produit_id],
                ['quantite' => 0, 'seuil_alerte' => 0, 'abattage_id' => $distribution->abattage_id]
            );

            $stock->increment('quantite', (float) $data['quantite_recue']);

            MouvementStock::create([
                'stock_id' => $stock->id,
                'user_id'  => $userId,
                'type'     => 'entree',
                'quantite' => $data['quantite_recue'],
                'motif'    => "Réception distribution #{$distribution->id}",
            ]);

            return $reception->fresh(['distribution.produit', 'distribution.fournisseurUser']);
        });
    }
}
