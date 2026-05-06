<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAchatFournisseurRequest;
use App\Http\Resources\AchatFournisseurResource;
use App\Services\AchatFournisseurService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * @group Achats Fournisseurs
 * @authenticated
 * Enregistrement des achats d'animaux auprès des fournisseurs.
 * Chaque achat crée automatiquement les animaux associés.
 */
class AchatFournisseurController extends Controller
{
    public function __construct(private readonly AchatFournisseurService $service) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        return AchatFournisseurResource::collection(
            $this->service->paginate($request->user()->boucherie_id)
        );
    }

    public function store(StoreAchatFournisseurRequest $request): JsonResponse
    {
        $achat = $this->service->create(
            $request->validated(),
            $request->user()->boucherie_id,
            $request->user()->id
        );

        return response()->json([
            'data'    => new AchatFournisseurResource($achat),
            'message' => 'Achat enregistré avec succès.',
        ], 201);
    }

    public function show(string $id): JsonResponse
    {
        return response()->json([
            'data' => new AchatFournisseurResource($this->service->findById($id)),
        ]);
    }
}
