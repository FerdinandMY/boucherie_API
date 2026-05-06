<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\AchatFournisseur;
use App\Models\Animal;
use App\Repositories\AchatFournisseurRepository;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class AchatFournisseurService
{
    public function __construct(private readonly AchatFournisseurRepository $repository) {}

    public function paginate(string $boucherieId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->paginate($boucherieId, $perPage);
    }

    public function findById(string $id): AchatFournisseur
    {
        return $this->repository->findOrFail($id);
    }

    public function create(array $data, string $boucherieId, int $userId): AchatFournisseur
    {
        return DB::transaction(function () use ($data, $boucherieId, $userId) {
            $animaux = $data['animaux'] ?? [];
            unset($data['animaux']);

            $data['boucherie_id'] = $boucherieId;
            $data['user_id']      = $userId;

            $achat = $this->repository->create($data);

            foreach ($animaux as $animalData) {
                $animalData['achat_fournisseur_id'] = $achat->id;
                $animalData['boucherie_id']         = $boucherieId;
                Animal::create($animalData);
            }

            $montantCalcule = array_sum(array_column($animaux, 'prix_achat'));
            if ($montantCalcule > 0 && empty($data['montant_total'])) {
                $achat->update(['montant_total' => $montantCalcule]);
            }

            return $achat->fresh('animaux');
        });
    }
}
