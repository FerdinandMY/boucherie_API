<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Typestock;
use Illuminate\Pagination\LengthAwarePaginator;

class TypeStockRepository
{
    public function __construct(
        private readonly Typestock $model
    ) {}

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->query()
            ->select(['id', 'type_name', 'description', 'created_at', 'updated_at'])
            ->latest()
            ->paginate($perPage);
    }

    public function getById(int $id): Typestock
    {
        return $this->model->query()->findOrFail($id);
    }

    public function create(array $data): Typestock
    {
        return $this->model->query()->create($data);
    }

    public function update(int $id, array $data): Typestock
    {
        $typeStock = $this->model->query()->findOrFail($id);
        $typeStock->update($data);

        return $typeStock;
    }

    public function delete(int $id): void
    {
        $this->model->query()->findOrFail($id)->delete();
    }
}
