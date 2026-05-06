<?php

namespace App\Http\Services;

use App\Models\Stock;

class StockService
{
    public function list(): \Illuminate\Database\Eloquent\Collection
    {
        return Stock::all();
    }

    public function find(int $id): Stock
    {
        return Stock::findOrFail($id);
    }

    public function create(array $data): Stock
    {
        return Stock::create($data);
    }

    public function update(Stock $stock, array $data): Stock
    {
        $stock->update($data);
        return $stock;
    }

    public function delete(Stock $stock): void
    {
        $stock->delete();
    }
}
