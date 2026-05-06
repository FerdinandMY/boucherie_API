<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAbattageRequest;
use App\Http\Resources\AbattageResource;
use App\Services\AbattageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * @group Abattages
 * @authenticated
 * Enregistrement des abattages. Met à jour le statut de l'animal et alimente le stock automatiquement.
 */
class AbattageController extends Controller
{
    public function __construct(private readonly AbattageService $service) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        return AbattageResource::collection(
            $this->service->paginate($request->user()->boucherie_id)
        );
    }

    public function store(StoreAbattageRequest $request): JsonResponse
    {
        $abattage = $this->service->create(
            $request->validated(),
            $request->user()->id
        );

        return response()->json([
            'data'    => new AbattageResource($abattage),
            'message' => 'Abattage enregistré avec succès.',
        ], 201);
    }

    public function show(string $id): JsonResponse
    {
        $abattage = $this->service->findById($id);
        $this->authorize('view', $abattage);

        return response()->json([
            'data' => new AbattageResource($abattage),
        ]);
    }
}
