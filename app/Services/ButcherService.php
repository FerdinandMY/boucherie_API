<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Butcher;
use App\Repositories\ButcherShopRepository;
use Illuminate\Pagination\LengthAwarePaginator;

class ButcherService
{
    public function __construct(
        private readonly ButcherShopRepository $repository
    ) {}

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->paginate($perPage);
    }

    public function findById(int $id): Butcher
    {
        return $this->repository->findOrFail($id);
    }

    public function create(array $data): Butcher
    {
        return $this->repository->create($data);
    }

    public function update(int $id, array $data): Butcher
    {
        return $this->repository->update($id, $data);
    }

    public function delete(int $id): void
    {
        $this->repository->delete($id);
    }
}
