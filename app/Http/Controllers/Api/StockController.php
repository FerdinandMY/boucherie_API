<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreStockRequest;
use App\Http\Requests\UpdateStockRequest;
use App\Http\Resources\StockResource;
use App\Models\Stock;
use App\Http\Services\StockService;

class StockController extends Controller
{
    public function __construct(protected StockService $service) {}

    public function index()
    {
        return StockResource::collection($this->service->list());
    }

    public function store(StoreStockRequest $request)
    {
        $stock = $this->service->create($request->validated());
        return new StockResource($stock);
    }

    public function show(Stock $stock)
    {
        return new StockResource($stock);
    }

    public function update(UpdateStockRequest $request, Stock $stock)
    {
        $updated = $this->service->update($stock, $request->validated());
        return new StockResource($updated);
    }

    public function destroy(Stock $stock)
    {
        $this->service->delete($stock);
        return response()->noContent();
    }
}

