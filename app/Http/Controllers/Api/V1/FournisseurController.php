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

    /**
     * Liste des fournisseurs
     *
     * @response {"data":[{"id":1,"boucherie_id":1,"nom":"Élevage Dupont","contact":"Jean Dupont","email":"jean@elevage.com","telephone":"+33612345678","adresse":"Route de la Ferme, 75001 Paris","created_at":"2024-01-15T10:00:00.000Z","updated_at":"2024-01-15T10:00:00.000Z"}],"links":{"first":"http://localhost/api/v1/fournisseurs?page=1","last":"http://localhost/api/v1/fournisseurs?page=1","prev":null,"next":null},"meta":{"current_page":1,"from":1,"last_page":1,"path":"http://localhost/api/v1/fournisseurs","per_page":15,"to":1,"total":1}}
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        return FournisseurResource::collection(
            $this->service->paginate($request->user()->boucherie_id)
        );
    }

    /**
     * Créer un fournisseur
     *
     * @response 201 {"data":{"id":1,"boucherie_id":1,"nom":"Élevage Dupont","contact":"Jean Dupont","email":"jean@elevage.com","telephone":"+33612345678","adresse":"Route de la Ferme, 75001 Paris","created_at":"2024-01-15T10:00:00.000Z","updated_at":"2024-01-15T10:00:00.000Z"},"message":"Fournisseur créé avec succès."}
     * @response 422 {"message":"The nom field is required.","errors":{"nom":["The nom field is required."]}}
     */
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

    /**
     * Détail d'un fournisseur
     *
     * @response {"data":{"id":1,"boucherie_id":1,"nom":"Élevage Dupont","contact":"Jean Dupont","email":"jean@elevage.com","telephone":"+33612345678","adresse":"Route de la Ferme, 75001 Paris","created_at":"2024-01-15T10:00:00.000Z","updated_at":"2024-01-15T10:00:00.000Z"}}
     * @response 404 {"message":"Not found."}
     */
    public function show(string $id): JsonResponse
    {
        $fournisseur = $this->service->findById($id);
        $this->authorize('view', $fournisseur);

        return response()->json([
            'data' => new FournisseurResource($fournisseur),
        ]);
    }

    /**
     * Mettre à jour un fournisseur
     *
     * @response {"data":{"id":1,"boucherie_id":1,"nom":"Élevage Dupont","contact":"Jean Dupont","email":"jean@elevage.com","telephone":"+33612345678","adresse":"Route de la Ferme, 75001 Paris","created_at":"2024-01-15T10:00:00.000Z","updated_at":"2024-01-15T10:00:00.000Z"},"message":"Fournisseur mis à jour avec succès."}
     * @response 404 {"message":"Not found."}
     * @response 422 {"message":"The nom field is required.","errors":{"nom":["The nom field is required."]}}
     */
    public function update(UpdateFournisseurRequest $request, string $id): JsonResponse
    {
        $fournisseur = $this->service->findById($id);
        $this->authorize('update', $fournisseur);

        $fournisseur = $this->service->update($id, $request->validated());

        return response()->json([
            'data'    => new FournisseurResource($fournisseur),
            'message' => 'Fournisseur mis à jour avec succès.',
        ]);
    }

    /**
     * Supprimer un fournisseur
     *
     * @response 204 {}
     * @response 404 {"message":"Not found."}
     */
    public function destroy(string $id): JsonResponse
    {
        $fournisseur = $this->service->findById($id);
        $this->authorize('delete', $fournisseur);

        $this->service->delete($id);

        return response()->json(null, 204);
    }
}
