<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\MouvementStock;
use App\Models\Stock;
use App\Repositories\StockRepository;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class StockService
{
    public function __construct(private readonly StockRepository $repository) {}

    public function paginate(string $boucherieId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->paginate($boucherieId, $perPage);
    }

    public function getById(string $id): Stock
    {
        return $this->repository->findOrFail($id);
    }

    public function create(array $data): Stock
    {
        return $this->repository->create($data);
    }

    public function update(string $id, array $data): Stock
    {
        return $this->repository->update($id, $data);
    }

    public function delete(string $id): void
    {
        $this->repository->findOrFail($id)->delete();
    }

    public function mouvements(string $id, int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->mouvements($id, $perPage);
    }

    public function ajuster(string $id, array $data, int $userId): Stock
    {
        return DB::transaction(function () use ($id, $data, $userId) {
            $stock    = $this->repository->findOrFail($id);
            $type     = $data['type'];
            $quantite = (float) $data['quantite'];

            if (in_array($type, ['sortie', 'perte'], true)) {
                if ((float) $stock->quantite < $quantite) {
                    throw ValidationException::withMessages([
                        'quantite' => ['Stock insuffisant pour effectuer cette opération.'],
                    ]);
                }
                $stock->decrement('quantite', $quantite);
            } else {
                $stock->increment('quantite', $quantite);
            }

            MouvementStock::create([
                'stock_id' => $stock->id,
                'user_id'  => $userId,
                'type'     => $type,
                'quantite' => $quantite,
                'motif'    => $data['motif'] ?? null,
            ]);

            return $stock->fresh('produit');
        });
    }
}
