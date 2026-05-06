<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTypeStockRequest;
use App\Http\Requests\UpdateTypeStockRequest;
use App\Http\Resources\TypeStockResource;
use App\Services\TypeStockService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TypeStockController extends Controller
{
    public function __construct(
        private readonly TypeStockService $service
    ) {}

    public function index(): AnonymousResourceCollection
    {
        return TypeStockResource::collection($this->service->paginate());
    }

    public function store(StoreTypeStockRequest $request): JsonResponse
    {
        $typeStock = $this->service->create($request->validated());

        return response()->json([
            'data'    => new TypeStockResource($typeStock),
            'message' => 'Type de stock créé avec succès.',
        ], 201);
    }

    public function show(int $id): JsonResponse
    {
        return response()->json([
            'data' => new TypeStockResource($this->service->getById($id)),
        ]);
    }

    public function update(UpdateTypeStockRequest $request, int $id): JsonResponse
    {
        $typeStock = $this->service->update($id, $request->validated());

        return response()->json([
            'data'    => new TypeStockResource($typeStock),
            'message' => 'Type de stock mis à jour avec succès.',
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->service->delete($id);

        return response()->json(null, 204);
    }
}
