<?php

// app/Repositories/TypeStockRepository.php

namespace App\Repositories;

use App\Models\TypeStock;
use App\Repositories\Interfaces\TypeStockRepositoryInterface;

class TypeStockRepository implements TypeStockRepositoryInterface
{
    public function all(): \Illuminate\Database\Eloquent\Collection
    {
        return TypeStock::all();
    }

    public function find($id)
    {
        return TypeStock::findOrFail($id);
    }

    public function create(array $data)
    {
        return TypeStock::create($data);
    }

    public function update($id, array $data)
    {
        $typeStock = TypeStock::findOrFail($id);
        $typeStock->update($data);
        return $typeStock;
    }

    public function delete($id): bool
    {
        $typeStock = TypeStock::findOrFail($id);
        $typeStock->delete();
        return true;
    }
}
