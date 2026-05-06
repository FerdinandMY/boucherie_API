<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreVenteRequest;
use App\Http\Requests\UpdateVenteStatutRequest;
use App\Http\Resources\VenteResource;
use App\Services\VenteService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * @group Ventes
 * @authenticated
 * Gestion des ventes. La création décrémente automatiquement le stock.
 * L'annulation restitue le stock et crée un mouvement d'entrée.
 */
class VenteController extends Controller
{
    public function __construct(private readonly VenteService $service) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        return VenteResource::collection(
            $this->service->paginate(
                $request->user()->boucherie_id,
                $request->only('statut', 'type_vente', 'date_debut', 'date_fin')
            )
        );
    }

    public function store(StoreVenteRequest $request): JsonResponse
    {
        $data               = $request->validated();
        $data['boucherie_id'] = $request->user()->boucherie_id;

        $vente = $this->service->create($data, $request->user()->id);

        return response()->json([
            'data'    => new VenteResource($vente),
            'message' => 'Vente créée avec succès.',
        ], 201);
    }

    public function show(string $id): JsonResponse
    {
        return response()->json([
            'data' => new VenteResource($this->service->findById($id)),
        ]);
    }

    public function updateStatut(UpdateVenteStatutRequest $request, string $id): JsonResponse
    {
        $vente = $this->service->updateStatut($id, $request->validated(), $request->user()->id);

        return response()->json([
            'data'    => new VenteResource($vente),
            'message' => 'Statut mis à jour avec succès.',
        ]);
    }

    public function destroy(string $id): JsonResponse
    {
        $this->service->delete($id);

        return response()->json(null, 204);
    }
}
