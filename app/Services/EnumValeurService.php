<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\EnumValeur;
use App\Repositories\EnumValeurRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\ValidationException;

class EnumValeurService
{
    public function __construct(private readonly EnumValeurRepository $repository) {}

    public function listByType(string $type, ?string $boucherieId = null): Collection
    {
        return $this->repository->listByType($type, $boucherieId);
    }

    public function create(string $type, array $data, ?string $boucherieId = null): EnumValeur
    {
        return $this->repository->create([
            'type'         => $type,
            'valeur'       => $data['valeur'],
            'libelle'      => $data['libelle'],
            'systeme'      => false,
            'boucherie_id' => $boucherieId,
            'ordre'        => $data['ordre'] ?? 0,
        ]);
    }

    public function update(string $id, array $data): EnumValeur
    {
        $valeur = $this->repository->findOrFail($id);

        if ($valeur->systeme && isset($data['valeur']) && $data['valeur'] !== $valeur->valeur) {
            throw ValidationException::withMessages([
                'valeur' => ['La valeur système ne peut pas être modifiée.'],
            ]);
        }

        $valeur->update(array_filter([
            'libelle' => $data['libelle'] ?? null,
            'ordre'   => $data['ordre'] ?? null,
        ], fn ($v) => $v !== null));

        return $valeur->fresh();
    }

    public function delete(string $id): void
    {
        $valeur = $this->repository->findOrFail($id);

        if ($valeur->systeme) {
            throw ValidationException::withMessages([
                'id' => ['Une valeur système ne peut pas être supprimée.'],
            ]);
        }

        $this->repository->delete($id);
    }
}
