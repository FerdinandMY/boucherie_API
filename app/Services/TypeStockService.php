<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Typestock;
use App\Repositories\TypeStockRepository;
use Illuminate\Pagination\LengthAwarePaginator;

class TypeStockService
{
    public function __construct(
        private readonly TypeStockRepository $repository
    ) {}

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->paginate($perPage);
    }

    public function getById(int $id): Typestock
    {
        return $this->repository->getById($id);
    }

    public function create(array $data): Typestock
    {
        return $this->repository->create($data);
    }

    public function update(int $id, array $data): Typestock
    {
        return $this->repository->update($id, $data);
    }

    public function delete(int $id): void
    {
        $this->repository->delete($id);
    }
}
