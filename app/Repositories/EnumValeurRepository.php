<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\EnumValeur;
use Illuminate\Database\Eloquent\Collection;

class EnumValeurRepository
{
    public function __construct(private readonly EnumValeur $model) {}

    public function listByType(string $type, ?string $boucherieId = null): Collection
    {
        return $this->model->query()
            ->where('type', $type)
            ->where(function ($q) use ($boucherieId) {
                $q->whereNull('boucherie_id');
                if ($boucherieId) {
                    $q->orWhere('boucherie_id', $boucherieId);
                }
            })
            ->orderBy('ordre')
            ->orderBy('libelle')
            ->get();
    }

    public function findOrFail(string $id): EnumValeur
    {
        return $this->model->query()->findOrFail($id);
    }

    public function create(array $data): EnumValeur
    {
        return $this->model->query()->create($data);
    }

    public function update(string $id, array $data): EnumValeur
    {
        $valeur = $this->findOrFail($id);
        $valeur->update($data);
        return $valeur;
    }

    public function delete(string $id): void
    {
        $this->findOrFail($id)->delete();
    }

    public function existsForType(string $type, string $valeur, ?string $boucherieId = null): bool
    {
        return $this->model->query()
            ->where('type', $type)
            ->where('valeur', $valeur)
            ->where(function ($q) use ($boucherieId) {
                $q->whereNull('boucherie_id');
                if ($boucherieId) {
                    $q->orWhere('boucherie_id', $boucherieId);
                }
            })
            ->exists();
    }
}
