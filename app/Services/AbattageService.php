<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Abattage;
use App\Models\MouvementStock;
use App\Models\Stock;
use App\Repositories\AbattageRepository;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AbattageService
{
    public function __construct(private readonly AbattageRepository $repository) {}

    public function paginate(?string $boucherieId = null, int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->paginate($boucherieId, $perPage);
    }

    public function paginateByFournisseurUser(int $userId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->paginateByFournisseurUser($userId, $perPage);
    }

    public function findById(string $id): Abattage
    {
        return $this->repository->findOrFail($id);
    }

    public function create(array $data, int $userId): Abattage
    {
        return DB::transaction(function () use ($data, $userId) {
            $animal = \App\Models\Animal::findOrFail($data['animal_id']);

            if ($animal->statut !== 'en_attente') {
                throw ValidationException::withMessages([
                    'animal_id' => ["Cet animal a déjà été abattu ou vendu."],
                ]);
            }

            if (empty($data['rendement_pct']) && isset($data['poids_carcasse_kg'])) {
                $data['rendement_pct'] = round(
                    ((float) $data['poids_carcasse_kg'] / (float) $animal->poids_vif_kg) * 100,
                    2
                );
            }

            $data['user_id'] = $userId;

            // Flux fournisseur : boucherie_id reste null, le stock sera alimenté à la réception
            // Flux boucherie   : boucherie_id provient de l'animal
            $isFournisseurFlow = $animal->isOwnedByFournisseur();
            if (! $isFournisseurFlow) {
                $data['boucherie_id'] = $animal->boucherie_id;
            }

            $abattage = $this->repository->create($data);

            $animal->update(['statut' => 'abattu']);

            // Alimentation directe du stock uniquement dans le flux boucherie
            if (! $isFournisseurFlow) {
                foreach ($data['stocks'] ?? [] as $stockData) {
                    $stock = Stock::firstOrCreate(
                        ['boucherie_id' => $abattage->boucherie_id, 'produit_id' => $stockData['produit_id']],
                        ['quantite' => 0, 'seuil_alerte' => $stockData['seuil_alerte'] ?? 0, 'abattage_id' => $abattage->id]
                    );

                    $stock->increment('quantite', (float) $stockData['quantite']);

                    MouvementStock::create([
                        'stock_id' => $stock->id,
                        'user_id'  => $userId,
                        'type'     => 'entree',
                        'quantite' => $stockData['quantite'],
                        'motif'    => "Abattage #{$abattage->id}",
                    ]);
                }
            }

            return $abattage->fresh(['animal', 'stocks.produit', 'distributions']);
        });
    }
}
