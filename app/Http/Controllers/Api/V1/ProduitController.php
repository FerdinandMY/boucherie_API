<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProduitRequest;
use App\Http\Requests\UpdateProduitRequest;
use App\Http\Resources\ProduitResource;
use App\Services\ProduitService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * @group Produits
 * @authenticated
 * Catalogue de produits vendus par la boucherie.
 */
class ProduitController extends Controller
{
    public function __construct(private readonly ProduitService $service) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        return ProduitResource::collection(
            $this->service->paginate($request->user()->boucherie_id)
        );
    }

    public function store(StoreProduitRequest $request): JsonResponse
    {
        $produit = $this->service->create(
            $request->validated(),
            $request->user()->boucherie_id
        );

        return response()->json([
            'data'    => new ProduitResource($produit),
            'message' => 'Produit créé avec succès.',
        ], 201);
    }

    public function show(string $id): JsonResponse
    {
        $produit = $this->service->findById($id);
        $this->authorize('view', $produit);

        return response()->json([
            'data' => new ProduitResource($produit),
        ]);
    }

    public function update(UpdateProduitRequest $request, string $id): JsonResponse
    {
        $produit = $this->service->findById($id);
        $this->authorize('update', $produit);

        $produit = $this->service->update($id, $request->validated());

        return response()->json([
            'data'    => new ProduitResource($produit),
            'message' => 'Produit mis à jour avec succès.',
        ]);
    }

    public function destroy(string $id): JsonResponse
    {
        $produit = $this->service->findById($id);
        $this->authorize('delete', $produit);

        $this->service->delete($id);

        return response()->json(null, 204);
    }
}
