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

    public function store(StoreLivraisonRequest $request, string $venteId): JsonResponse
    {
        $livraison = $this->service->create($venteId, $request->validated(), $request->user()->id);

        return response()->json([
            'data'    => new LivraisonResource($livraison),
            'message' => 'Livraison créée avec succès.',
        ], 201);
    }

    public function update(UpdateLivraisonRequest $request, string $venteId): JsonResponse
    {
        $livraison = $this->service->update($venteId, $request->validated());

        return response()->json([
            'data'    => new LivraisonResource($livraison),
            'message' => 'Livraison mise à jour avec succès.',
        ]);
    }
}
