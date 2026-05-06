<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Client;
use Illuminate\Pagination\LengthAwarePaginator;

class ClientRepository
{
    public function __construct(private readonly Client $model) {}

    public function paginate(string $boucherieId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->query()
            ->where('boucherie_id', $boucherieId)
            ->select(['id', 'boucherie_id', 'nom', 'telephone', 'adresse', 'created_at', 'updated_at'])
            ->latest()
            ->paginate($perPage);
    }

    public function findOrFail(string $id): Client
    {
        return $this->model->query()->with('ventes')->findOrFail($id);
    }

    public function create(array $data): Client
    {
        return $this->model->query()->create($data);
    }

    public function update(string $id, array $data): Client
    {
        $client = $this->model->query()->findOrFail($id);
        $client->update($data);
        return $client;
    }
}
