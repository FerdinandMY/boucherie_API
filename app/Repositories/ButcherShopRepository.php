<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Butcher;
use Illuminate\Pagination\LengthAwarePaginator;

class ButcherShopRepository
{
    public function __construct(
        private readonly Butcher $model
    ) {}

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->query()
            ->select(['id', 'name', 'address', 'city', 'postal_code', 'phone', 'email',
                      'opening_hours', 'website', 'owner', 'specialties',
                      'average_rating', 'review_count', 'created_at', 'updated_at'])
            ->latest()
            ->paginate($perPage);
    }

    public function findOrFail(int $id): Butcher
    {
        return $this->model->query()->findOrFail($id);
    }

    public function create(array $data): Butcher
    {
        return $this->model->query()->create($data);
    }

    public function update(int $id, array $data): Butcher
    {
        $butcher = $this->findOrFail($id);
        $butcher->update($data);

        return $butcher;
    }

    public function delete(int $id): void
    {
        $this->findOrFail($id)->delete();
    }
}
