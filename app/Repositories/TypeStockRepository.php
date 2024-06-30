<?php

// app/Repositories/TypeStockRepository.php

namespace App\Repositories;

use App\Models\TypeStock;

class TypeStockRepository
{
    public function getAll(): \Illuminate\Database\Eloquent\Collection
    {
        return TypeStock::all();
    }

    public function getById($id)
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

    public function delete($id)
    {
        $typeStock = TypeStock::findOrFail($id);
        $typeStock->delete();
        return $typeStock;
    }
}
