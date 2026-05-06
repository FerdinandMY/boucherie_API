<?php

namespace App\Http\Services;

use App\Models\Category;

class CategoryService
{
    public function list()
    {
        return Category::withCount('products')->get();
    }

    public function create(array $data): Category
    {
        return Category::create($data);
    }

    public function find(int $id): Category
    {
        return Category::with('products')->findOrFail($id);
    }

    public function update(Category $category, array $data): Category
    {
        $category->update($data);
        return $category;
    }

    public function delete(Category $category): void
    {
        $category->delete();
    }
}

