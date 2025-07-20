<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Http\Services\ProductService;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct(protected ProductService $service) {}

    public function index(Request $request): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        return ProductResource::collection(
            $this->service->filter(
                $request->query('category_id'),
                $request->query('search')
            )
        );
    }

    public function store(StoreProductRequest $request)
    {
        $product = $this->service->create($request->validated());
        return new ProductResource($product);
    }

    public function show(Product $product)
    {
        return new ProductResource($product->load('category'));
    }

    public function update(UpdateProductRequest $request, Product $product)
    {
        $updated = $this->service->update($product, $request->validated());
        return new ProductResource($updated);
    }

    public function destroy(Product $product)
    {
        $this->service->delete($product);
        return response()->noContent();
    }
}

