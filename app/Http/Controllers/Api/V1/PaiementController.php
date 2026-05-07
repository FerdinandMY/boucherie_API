<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePaiementRequest;
use App\Http\Resources\PaiementResource;
use App\Services\PaiementService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * @group Paiements
 * @authenticated
 * Gestion des paiements d'une vente. Le total des paiements ne peut pas dépasser le montant de la vente.
 */
class PaiementController extends Controller
{
    public function __construct(private readonly PaiementService $service) {}

    /**
     * Liste des paiements d'une vente
     *
     * @urlParam vente integer required ID de la vente. Exemple: 1
     * @response {"data":[{"id":1,"vente_id":1,"user_id":1,"mode_paiement":"especes","montant":12500,"date_paiement":"2024-01-17","created_at":"2024-01-17T14:30:00.000Z"}],"links":{"first":"http://localhost/api/v1/ventes/1/paiements?page=1","last":"http://localhost/api/v1/ventes/1/paiements?page=1","prev":null,"next":null},"meta":{"current_page":1,"from":1,"last_page":1,"path":"http://localhost/api/v1/ventes/1/paiements","per_page":15,"to":1,"total":1}}
     * @response 404 {"message":"Not found."}
     */
    public function index(Request $request, string $venteId): AnonymousResourceCollection
    {
        return PaiementResource::collection(
            $this->service->paginateByVente($venteId)
        );
    }

    /**
     * Enregistrer un paiement
     *
     * Le total des paiements ne peut pas dépasser le montant de la vente.
     *
     * @urlParam vente integer required ID de la vente. Exemple: 1
     * @response 201 {"data":{"id":1,"vente_id":1,"user_id":1,"mode_paiement":"especes","montant":12500,"date_paiement":"2024-01-17","created_at":"2024-01-17T14:30:00.000Z"},"message":"Paiement enregistré avec succès."}
     * @response 422 {"message":"The montant field is required.","errors":{"montant":["The montant field is required."]}}
     * @response 404 {"message":"Not found."}
     */
    public function store(StorePaiementRequest $request, string $venteId): JsonResponse
    {
        $paiement = $this->service->create($venteId, $request->validated(), $request->user()->id);

        return response()->json([
            'data'    => new PaiementResource($paiement),
            'message' => 'Paiement enregistré avec succès.',
        ], 201);
    }
}
