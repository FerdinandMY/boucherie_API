<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTypeStockRequest;
use App\Http\Requests\UpdateTypeStockRequest;
use App\Http\Resources\TypeStockResource;
use App\Http\Services\TypeStockService;
use App\Models\Typestock;
use Illuminate\Http\Request;

class TypestockController extends Controller
{
    protected $service;

    public function __construct(TypeStockService $service)
    {
        $this->service = $service;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return TypeStockResource::collection($this->service->getAll());
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
    public function store(StoreTypeStockRequest $request)
    {
        $data = $request->validated();
        return new TypeStockResource($this->service->create($data));
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        return new TypeStockResource($this->service->getById($id));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Typestock $typestock)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTypeStockRequest $request, $id)
    {
        $data = $request->validated();
        return new TypeStockResource($this->service->update($id, $data));
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
