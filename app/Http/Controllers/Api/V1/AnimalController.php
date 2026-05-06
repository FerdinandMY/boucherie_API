<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\AnimalResource;
use App\Services\AnimalService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * @group Animaux
 * @authenticated
 * Consultation des animaux (créés via les achats fournisseurs).
 * Filtrable par statut : `en_attente`, `abattu`, `vendu`.
 */
class AnimalController extends Controller
{
    public function __construct(private readonly AnimalService $service) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        return AnimalResource::collection(
            $this->service->paginate($request->user()->boucherie_id, $request->only('statut'))
        );
    }

    public function show(string $id): JsonResponse
    {
        $animal = $this->service->findById($id);
        $this->authorize('view', $animal);

        return response()->json([
            'data' => new AnimalResource($animal),
        ]);
    }
}
