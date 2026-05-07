<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreClientRequest;
use App\Http\Requests\UpdateClientRequest;
use App\Http\Resources\ClientResource;
use App\Services\ClientService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * @group Clients
 * @authenticated
 * Gestion de la clientèle de la boucherie.
 */
class ClientController extends Controller
{
    public function __construct(private readonly ClientService $service) {}

    /**
     * Liste des clients
     *
     * @response {"data":[{"id":1,"boucherie_id":1,"nom":"Restaurant Le Bon Goût","telephone":"+33612345678","email":"contact@lebongout.fr","adresse":"5 avenue de la Gastronomie, 75002 Paris","created_at":"2024-01-15T10:00:00.000Z","updated_at":"2024-01-15T10:00:00.000Z"}],"links":{"first":"http://localhost/api/v1/clients?page=1","last":"http://localhost/api/v1/clients?page=1","prev":null,"next":null},"meta":{"current_page":1,"from":1,"last_page":1,"path":"http://localhost/api/v1/clients","per_page":15,"to":1,"total":1}}
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        return ClientResource::collection(
            $this->service->paginate($request->user()->boucherie_id)
        );
    }

    /**
     * Créer un client
     *
     * @response 201 {"data":{"id":1,"boucherie_id":1,"nom":"Restaurant Le Bon Goût","telephone":"+33612345678","email":"contact@lebongout.fr","adresse":"5 avenue de la Gastronomie, 75002 Paris","created_at":"2024-01-15T10:00:00.000Z","updated_at":"2024-01-15T10:00:00.000Z"},"message":"Client créé avec succès."}
     * @response 422 {"message":"The nom field is required.","errors":{"nom":["The nom field is required."]}}
     */
    public function store(StoreClientRequest $request): JsonResponse
    {
        $client = $this->service->create(
            $request->validated(),
            $request->user()->boucherie_id
        );

        return response()->json([
            'data'    => new ClientResource($client),
            'message' => 'Client créé avec succès.',
        ], 201);
    }

    /**
     * Détail d'un client
     *
     * @response {"data":{"id":1,"boucherie_id":1,"nom":"Restaurant Le Bon Goût","telephone":"+33612345678","email":"contact@lebongout.fr","adresse":"5 avenue de la Gastronomie, 75002 Paris","created_at":"2024-01-15T10:00:00.000Z","updated_at":"2024-01-15T10:00:00.000Z"}}
     * @response 404 {"message":"Not found."}
     */
    public function show(string $id): JsonResponse
    {
        $client = $this->service->findById($id);
        $this->authorize('view', $client);

        return response()->json([
            'data' => new ClientResource($client),
        ]);
    }

    /**
     * Mettre à jour un client
     *
     * @response {"data":{"id":1,"boucherie_id":1,"nom":"Restaurant Le Bon Goût","telephone":"+33612345678","email":"contact@lebongout.fr","adresse":"5 avenue de la Gastronomie, 75002 Paris","created_at":"2024-01-15T10:00:00.000Z","updated_at":"2024-01-15T10:00:00.000Z"},"message":"Client mis à jour avec succès."}
     * @response 404 {"message":"Not found."}
     * @response 422 {"message":"The nom field is required.","errors":{"nom":["The nom field is required."]}}
     */
    public function update(UpdateClientRequest $request, string $id): JsonResponse
    {
        $client = $this->service->findById($id);
        $this->authorize('update', $client);

        $client = $this->service->update($id, $request->validated());

        return response()->json([
            'data'    => new ClientResource($client),
            'message' => 'Client mis à jour avec succès.',
        ]);
    }

    /**
     * Supprimer un client
     *
     * @response 204 {}
     * @response 404 {"message":"Not found."}
     */
    public function destroy(string $id): JsonResponse
    {
        $client = $this->service->findById($id);
        $this->authorize('delete', $client);

        $this->service->delete($id);

        return response()->json(null, 204);
    }
}
