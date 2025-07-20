<?php

namespace App\Http\Services;

use App\Models\Product;

class ProductService
{
    public function list(): \Illuminate\Database\Eloquent\Collection|array
    {
        return Product::with('category')->get();
    }

    public function create(array $data): Product
    {
        return Product::create($data);
    }

    public function find(int $id): Product
    {
        return Product::with('category')->findOrFail($id);
    }

    public function update(Product $product, array $data): Product
    {
        $product->update($data);
        return $product;
    }

    public function delete(Product $product): void
    {
        $product->delete();
    }

    public function filter(?int $categoryId = null, ?string $search = null)
    {
        return Product::when($categoryId, fn($q) => $q->where('category_id', $categoryId))
            ->when($search, fn($q) => $q->where('name', 'like', "%$search%"))
            ->with('category')
            ->get();
    }
}

