<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Client;
use App\Repositories\ClientRepository;
use Illuminate\Pagination\LengthAwarePaginator;

class ClientService
{
    public function __construct(private readonly ClientRepository $repository) {}

    public function paginate(string $boucherieId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->paginate($boucherieId, $perPage);
    }

    public function findById(string $id): Client
    {
        return $this->repository->findOrFail($id);
    }

    public function create(array $data, string $boucherieId): Client
    {
        return $this->repository->create(array_merge($data, ['boucherie_id' => $boucherieId]));
    }

    public function update(string $id, array $data): Client
    {
        return $this->repository->update($id, $data);
    }

    public function delete(string $id): void
    {
        $client = $this->repository->findOrFail($id);
        $client->delete();
    }
}
