<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAbattageRequest;
use App\Http\Resources\AbattageResource;
use App\Services\AbattageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * @group Abattages
 * @authenticated
 * Enregistrement des abattages. Met à jour le statut de l'animal et alimente le stock automatiquement.
 */
class AbattageController extends Controller
{
    public function __construct(private readonly AbattageService $service) {}

    /**
     * Liste des abattages
     *
     * @response {"data":[{"id":1,"boucherie_id":1,"animal_id":1,"animal":null,"user_id":1,"date_abattage":"2024-01-16","poids_carcasse_kg":210.5,"rendement_pct":60.0,"notes":"Abattage standard","stocks":[],"created_at":"2024-01-16T06:00:00.000Z","updated_at":"2024-01-16T06:00:00.000Z"}],"links":{"first":"http://localhost/api/v1/abattages?page=1","last":"http://localhost/api/v1/abattages?page=1","prev":null,"next":null},"meta":{"current_page":1,"from":1,"last_page":1,"path":"http://localhost/api/v1/abattages","per_page":15,"to":1,"total":1}}
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        return AbattageResource::collection(
            $this->service->paginate($request->user()->boucherie_id)
        );
    }

    /**
     * Enregistrer un abattage
     *
     * Met à jour le statut de l'animal en `abattu` et alimente le stock automatiquement.
     *
     * @response 201 {"data":{"id":1,"boucherie_id":1,"animal_id":1,"animal":{"id":1,"boucherie_id":1,"achat_fournisseur_id":1,"espece":"bovin","poids_vif_kg":350.5,"prix_achat":180000,"numero_tag":"TAG-2024-001","statut":"abattu","created_at":"2024-01-15T10:00:00.000Z","updated_at":"2024-01-16T06:00:00.000Z"},"user_id":1,"date_abattage":"2024-01-16","poids_carcasse_kg":210.5,"rendement_pct":60.0,"notes":"Abattage standard","stocks":[{"id":1,"boucherie_id":1,"produit_id":1,"produit":null,"abattage_id":1,"quantite":210.5,"seuil_alerte":10,"en_alerte":false,"created_at":"2024-01-16T06:00:00.000Z","updated_at":"2024-01-16T06:00:00.000Z"}],"created_at":"2024-01-16T06:00:00.000Z","updated_at":"2024-01-16T06:00:00.000Z"},"message":"Abattage enregistré avec succès."}
     * @response 422 {"message":"The animal_id field is required.","errors":{"animal_id":["The animal_id field is required."]}}
     */
    public function store(StoreAbattageRequest $request): JsonResponse
    {
        $abattage = $this->service->create(
            $request->validated(),
            $request->user()->id
        );

        return response()->json([
            'data'    => new AbattageResource($abattage),
            'message' => 'Abattage enregistré avec succès.',
        ], 201);
    }

    /**
     * Détail d'un abattage
     *
     * @response {"data":{"id":1,"boucherie_id":1,"animal_id":1,"animal":{"id":1,"boucherie_id":1,"achat_fournisseur_id":1,"espece":"bovin","poids_vif_kg":350.5,"prix_achat":180000,"numero_tag":"TAG-2024-001","statut":"abattu","created_at":"2024-01-15T10:00:00.000Z","updated_at":"2024-01-16T06:00:00.000Z"},"user_id":1,"date_abattage":"2024-01-16","poids_carcasse_kg":210.5,"rendement_pct":60.0,"notes":"Abattage standard","stocks":[],"created_at":"2024-01-16T06:00:00.000Z","updated_at":"2024-01-16T06:00:00.000Z"}}
     * @response 404 {"message":"Not found."}
     */
    public function show(string $id): JsonResponse
    {
        $abattage = $this->service->findById($id);
        $this->authorize('view', $abattage);

        return response()->json([
            'data' => new AbattageResource($abattage),
        ]);
    }
}
