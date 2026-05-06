<?php

namespace App\Http\Controllers\Api;


namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use App\Http\Services\CategoryService;

class CategoryController extends Controller
{
    public function __construct(protected CategoryService $service) {}

    public function index(): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        return CategoryResource::collection($this->service->list());
    }

    public function store(StoreCategoryRequest $request): CategoryResource
    {
        $category = $this->service->create($request->validated());
        return new CategoryResource($category);
    }

    public function show(Category $category): CategoryResource
    {
        return new CategoryResource($this->service->find($category->id));
    }

    public function update(UpdateCategoryRequest $request, Category $category): CategoryResource
    {
        $updated = $this->service->update($category, $request->validated());
        return new CategoryResource($updated);
    }

    public function destroy(Category $category): \Illuminate\Http\Response
    {
        $this->service->delete($category);
        return response()->noContent();
    }
}
