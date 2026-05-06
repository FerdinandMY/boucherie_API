<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreFournisseurRequest;
use App\Http\Requests\UpdateFournisseurRequest;
use App\Http\Resources\FournisseurResource;
use App\Services\FournisseurService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * @group Fournisseurs
 * @authenticated
 * Gestion des fournisseurs d'animaux et de produits.
 */
class FournisseurController extends Controller
{
    public function __construct(private readonly FournisseurService $service) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        return FournisseurResource::collection(
            $this->service->paginate($request->user()->boucherie_id)
        );
    }

    public function store(StoreFournisseurRequest $request): JsonResponse
    {
        $fournisseur = $this->service->create(
            $request->validated(),
            $request->user()->boucherie_id
        );

        return response()->json([
            'data'    => new FournisseurResource($fournisseur),
            'message' => 'Fournisseur créé avec succès.',
        ], 201);
    }

    public function show(string $id): JsonResponse
    {
        return response()->json([
            'data' => new FournisseurResource($this->service->findById($id)),
        ]);
    }

    public function update(UpdateFournisseurRequest $request, string $id): JsonResponse
    {
        $fournisseur = $this->service->update($id, $request->validated());

        return response()->json([
            'data'    => new FournisseurResource($fournisseur),
            'message' => 'Fournisseur mis à jour avec succès.',
        ]);
    }

    public function destroy(string $id): JsonResponse
    {
        $this->service->delete($id);

        return response()->json(null, 204);
    }
}
