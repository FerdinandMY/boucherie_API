<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Animal;
use App\Repositories\AnimalRepository;
use Illuminate\Pagination\LengthAwarePaginator;

class AnimalService
{
    public function __construct(private readonly AnimalRepository $repository) {}

    public function paginate(string $boucherieId, array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->paginate($boucherieId, $filters, $perPage);
    }

    public function findById(string $id): Animal
    {
        return $this->repository->findOrFail($id);
    }
}
