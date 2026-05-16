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

    /**
     * Liste des produits
     *
     * @response {"data":[{"id":1,"boucherie_id":1,"nom":"Côte de bœuf","categorie":"boeuf","unite":"kg","prix_unitaire":2500,"description":"Côte à l'os maturée 21 jours","created_at":"2024-01-15T10:00:00.000Z","updated_at":"2024-01-15T10:00:00.000Z"}],"links":{"first":"http://localhost/api/v1/produits?page=1","last":"http://localhost/api/v1/produits?page=1","prev":null,"next":null},"meta":{"current_page":1,"from":1,"last_page":1,"path":"http://localhost/api/v1/produits","per_page":15,"to":1,"total":1}}
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $user = $request->user();

        $boucherieId = match (true) {
            $user->hasRole('admin')       => null,
            $user->hasRole('fournisseur') => null,
            default                       => $user->boucherie_id,
        };

        return ProduitResource::collection(
            $this->service->paginate($boucherieId)
        );
    }

    /**
     * Créer un produit
     *
     * @response 201 {"data":{"id":1,"boucherie_id":1,"nom":"Côte de bœuf","categorie":"boeuf","unite":"kg","prix_unitaire":2500,"description":"Côte à l'os maturée 21 jours","created_at":"2024-01-15T10:00:00.000Z","updated_at":"2024-01-15T10:00:00.000Z"},"message":"Produit créé avec succès."}
     * @response 422 {"message":"The nom field is required.","errors":{"nom":["The nom field is required."]}}
     */
    public function store(StoreProduitRequest $request): JsonResponse
    {
        $user        = $request->user();
        $boucherieId = $user->hasRole('admin')
            ? $request->validated('boucherie_id')
            : $user->boucherie_id;

        $produit = $this->service->create(
            $request->safe()->except('boucherie_id'),
            $boucherieId
        );

        return response()->json([
            'data'    => new ProduitResource($produit),
            'message' => 'Produit créé avec succès.',
        ], 201);
    }

    /**
     * Détail d'un produit
     *
     * @response {"data":{"id":1,"boucherie_id":1,"nom":"Côte de bœuf","categorie":"boeuf","unite":"kg","prix_unitaire":2500,"description":"Côte à l'os maturée 21 jours","created_at":"2024-01-15T10:00:00.000Z","updated_at":"2024-01-15T10:00:00.000Z"}}
     * @response 404 {"message":"Not found."}
     */
    public function show(string $id): JsonResponse
    {
        $produit = $this->service->findById($id);
        $this->authorize('view', $produit);

        return response()->json([
            'data' => new ProduitResource($produit),
        ]);
    }

    /**
     * Mettre à jour un produit
     *
     * @response {"data":{"id":1,"boucherie_id":1,"nom":"Côte de bœuf","categorie":"boeuf","unite":"kg","prix_unitaire":2500,"description":"Côte à l'os maturée 21 jours","created_at":"2024-01-15T10:00:00.000Z","updated_at":"2024-01-15T10:00:00.000Z"},"message":"Produit mis à jour avec succès."}
     * @response 404 {"message":"Not found."}
     * @response 422 {"message":"The nom field is required.","errors":{"nom":["The nom field is required."]}}
     */
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

    /**
     * Supprimer un produit
     *
     * @response 204 {}
     * @response 404 {"message":"Not found."}
     */
    public function destroy(string $id): JsonResponse
    {
        $produit = $this->service->findById($id);
        $this->authorize('delete', $produit);

        $this->service->delete($id);

        return response()->json(null, 204);
    }
}
