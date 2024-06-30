<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreStockRequest;
use App\Http\Requests\UpdateStockRequest;
use App\Http\Resources\StockResource;
use App\Http\Services\StockService;
use App\Models\Stock;
use Illuminate\Http\Request;

class StockController extends Controller
{

    protected StockService $service;

    public function __construct(StockService $service)
    {
        $this->service = $service;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        return StockResource::collection($this->service->getAll());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreStockRequest $request): StockResource
    {
        $data = $request->validated();
        return new StockResource($this->service->create($data));
    }

    /**
     * Display the specified resource.
     */
    public function show($id): StockResource
    {
        return new StockResource($this->service->getById($id));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Stock $stock)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateStockRequest $request, $id)
    {
        $data = $request->validated();
        return new StockResource($this->service->update($id, $data));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $this->service->delete($id);
        return response()->json(null, 204);
    }
}
