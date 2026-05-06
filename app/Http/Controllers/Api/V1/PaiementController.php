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

    public function index(Request $request, string $venteId): AnonymousResourceCollection
    {
        return PaiementResource::collection(
            $this->service->paginateByVente($venteId)
        );
    }

    public function store(StorePaiementRequest $request, string $venteId): JsonResponse
    {
        $paiement = $this->service->create($venteId, $request->validated(), $request->user()->id);

        return response()->json([
            'data'    => new PaiementResource($paiement),
            'message' => 'Paiement enregistré avec succès.',
        ], 201);
    }
}
