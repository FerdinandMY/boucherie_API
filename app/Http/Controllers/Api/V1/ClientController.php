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

    public function index(Request $request): AnonymousResourceCollection
    {
        return ClientResource::collection(
            $this->service->paginate($request->user()->boucherie_id)
        );
    }

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

    public function show(string $id): JsonResponse
    {
        $client = $this->service->findById($id);
        $this->authorize('view', $client);

        return response()->json([
            'data' => new ClientResource($client),
        ]);
    }

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

    public function destroy(string $id): JsonResponse
    {
        $client = $this->service->findById($id);
        $this->authorize('delete', $client);

        $this->service->delete($id);

        return response()->json(null, 204);
    }
}
