<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateButcherRequest;
use App\Http\Requests\UpdateButcherRequest;
use App\Http\Resources\ButcherResource;
use App\Services\ButcherService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ButcherController extends Controller
{
    public function __construct(
        private readonly ButcherService $service
    ) {}

    public function index(): AnonymousResourceCollection
    {
        return ButcherResource::collection($this->service->paginate());
    }

    public function store(CreateButcherRequest $request): JsonResponse
    {
        $butcher = $this->service->create($request->validated());

        return response()->json([
            'data'    => new ButcherResource($butcher),
            'message' => 'Boucherie créée avec succès.',
        ], 201);
    }

    public function show(int $id): JsonResponse
    {
        return response()->json([
            'data' => new ButcherResource($this->service->findById($id)),
        ]);
    }

    public function update(UpdateButcherRequest $request, int $id): JsonResponse
    {
        $butcher = $this->service->update($id, $request->validated());

        return response()->json([
            'data'    => new ButcherResource($butcher),
            'message' => 'Boucherie mise à jour avec succès.',
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->service->delete($id);

        return response()->json(null, 204);
    }
}
