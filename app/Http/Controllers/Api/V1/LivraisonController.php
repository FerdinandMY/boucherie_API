<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLivraisonRequest;
use App\Http\Requests\UpdateLivraisonRequest;
use App\Http\Resources\LivraisonResource;
use App\Services\LivraisonService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @group Livraisons
 * @authenticated
 * Gestion de la livraison associée à une vente (une livraison par vente maximum).
 */
class LivraisonController extends Controller
{
    public function __construct(private readonly LivraisonService $service) {}

    /**
     * Créer une livraison
     *
     * Une seule livraison par vente. La vente doit être de type `livraison`.
     *
     * @urlParam vente integer required ID de la vente. Exemple: 1
     * @response 201 {"data":{"id":1,"vente_id":1,"user_id":1,"adresse_livraison":"5 avenue de la Gastronomie, 75002 Paris","statut":"en_attente","date_prevue":"2024-01-18","created_at":"2024-01-17T14:00:00.000Z","updated_at":"2024-01-17T14:00:00.000Z"},"message":"Livraison créée avec succès."}
     * @response 422 {"message":"The adresse_livraison field is required.","errors":{"adresse_livraison":["The adresse_livraison field is required."]}}
     * @response 404 {"message":"Not found."}
     */
    public function store(StoreLivraisonRequest $request, string $venteId): JsonResponse
    {
        $livraison = $this->service->create($venteId, $request->validated(), $request->user()->id);

        return response()->json([
            'data'    => new LivraisonResource($livraison),
            'message' => 'Livraison créée avec succès.',
        ], 201);
    }

    /**
     * Mettre à jour une livraison
     *
     * @urlParam vente integer required ID de la vente. Exemple: 1
     * @response {"data":{"id":1,"vente_id":1,"user_id":1,"adresse_livraison":"5 avenue de la Gastronomie, 75002 Paris","statut":"en_cours","date_prevue":"2024-01-18","created_at":"2024-01-17T14:00:00.000Z","updated_at":"2024-01-17T16:00:00.000Z"},"message":"Livraison mise à jour avec succès."}
     * @response 404 {"message":"Not found."}
     * @response 422 {"message":"The statut field is required.","errors":{"statut":["The statut field is required."]}}
     */
    public function update(UpdateLivraisonRequest $request, string $venteId): JsonResponse
    {
        $livraison = $this->service->update($venteId, $request->validated());

        return response()->json([
            'data'    => new LivraisonResource($livraison),
            'message' => 'Livraison mise à jour avec succès.',
        ]);
    }
}
