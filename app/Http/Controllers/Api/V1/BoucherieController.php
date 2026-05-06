<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBoucherieRequest;
use App\Http\Requests\UpdateBoucherieRequest;
use App\Http\Resources\BoucherieResource;
use App\Services\BoucherieService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * @group Boucheries
 * @authenticated
 * Gestion des établissements (admin uniquement).
 */
class BoucherieController extends Controller
{
    public function __construct(private readonly BoucherieService $service) {}

    public function index(): AnonymousResourceCollection
    {
        return BoucherieResource::collection($this->service->paginate());
    }

    public function store(StoreBoucherieRequest $request): JsonResponse
    {
        $boucherie = $this->service->create($request->validated());

        return response()->json([
            'data'    => new BoucherieResource($boucherie),
            'message' => 'Boucherie créée avec succès.',
        ], 201);
    }

    public function show(string $id): JsonResponse
    {
        return response()->json([
            'data' => new BoucherieResource($this->service->findById($id)),
        ]);
    }

    public function update(UpdateBoucherieRequest $request, string $id): JsonResponse
    {
        $boucherie = $this->service->update($id, $request->validated());

        return response()->json([
            'data'    => new BoucherieResource($boucherie),
            'message' => 'Boucherie mise à jour avec succès.',
        ]);
    }

    public function destroy(string $id): JsonResponse
    {
        $this->service->delete($id);

        return response()->json(null, 204);
    }
}
