<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreButcherRequest;
use App\Http\Requests\UpdateButcherRequest;
use App\Http\Resources\ButcherResource;
use App\Http\Services\ButcherService;
use App\Models\Butcher;

class ButcherController extends Controller
{
    public function __construct(protected ButcherService $service) {}

    public function index()
    {
        return ButcherResource::collection($this->service->list());
    }

    public function store(StoreButcherRequest $request)
    {
        $butcher = $this->service->create($request->validated());
        return new ButcherResource($butcher);
    }

    public function show(Butcher $butcher)
    {
        return new ButcherResource($butcher);
    }

    public function update(UpdateButcherRequest $request, Butcher $butcher)
    {
        $updated = $this->service->update($butcher, $request->validated());
        return new ButcherResource($updated);
    }

    public function destroy(Butcher $butcher)
    {
        $this->service->delete($butcher);
        return response()->noContent();
    }
}

