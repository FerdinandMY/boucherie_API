<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDistributionRequest;
use App\Http\Resources\DistributionResource;
use App\Services\DistributionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * @group Distributions
 * @authenticated
 * Le fournisseur distribue les lots issus d'un abattage vers les boucheries.
 * La boucherie peut consulter les distributions qui lui sont destinées.
 */
class DistributionController extends Controller
{
    public function __construct(private readonly DistributionService $service) {}

    /**
     * Liste des distributions
     *
     * Fournisseur : toutes ses distributions. Boucher/Admin : celles destinées à sa boucherie.
     *
     * @response {"data":[{"id":"uuid","abattage_id":"uuid","fournisseur_user_id":3,"boucherie_id":"uuid","produit_id":"uuid","quantite":"45.000","statut":"en_attente","notes":null,"reception":null,"created_at":"2024-01-16T08:00:00.000Z","updated_at":"2024-01-16T08:00:00.000Z"}],"links":{"first":"http://localhost/api/v1/distributions?page=1","last":"http://localhost/api/v1/distributions?page=1","prev":null,"next":null},"meta":{"current_page":1,"from":1,"last_page":1,"path":"http://localhost/api/v1/distributions","per_page":15,"to":1,"total":1}}
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $user = $request->user();

        $paginator = $user->hasRole('fournisseur')
            ? $this->service->paginateByFournisseur($user->id)
            : $this->service->paginateByBoucherie($user->boucherie_id);

        return DistributionResource::collection($paginator);
    }

    /**
     * Créer une distribution
     *
     * Le fournisseur déclare un lot de viande destiné à une boucherie depuis un abattage.
     *
     * @response 201 {"data":{"id":"uuid","abattage_id":"uuid","fournisseur_user_id":3,"boucherie_id":"uuid","produit_id":"uuid","quantite":"45.000","statut":"en_attente","notes":"Côtes et filets","reception":null,"created_at":"2024-01-16T08:00:00.000Z","updated_at":"2024-01-16T08:00:00.000Z"},"message":"Distribution créée avec succès."}
     * @response 422 {"message":"The abattage_id field is required.","errors":{"abattage_id":["The abattage_id field is required."]}}
     */
    public function store(StoreDistributionRequest $request): JsonResponse
    {
        $distribution = $this->service->create($request->validated(), $request->user()->id);

        return response()->json([
            'data'    => new DistributionResource($distribution),
            'message' => 'Distribution créée avec succès.',
        ], 201);
    }

    /**
     * Détail d'une distribution
     *
     * @response {"data":{"id":"uuid","abattage_id":"uuid","fournisseur_user_id":3,"boucherie_id":"uuid","produit_id":"uuid","quantite":"45.000","statut":"en_attente","notes":null,"reception":null,"created_at":"2024-01-16T08:00:00.000Z","updated_at":"2024-01-16T08:00:00.000Z"}}
     * @response 404 {"message":"Not found."}
     */
    public function show(string $id): JsonResponse
    {
        return response()->json([
            'data' => new DistributionResource($this->service->findById($id)),
        ]);
    }

    /**
     * Annuler une distribution (fournisseur)
     *
     * Seul le fournisseur propriétaire peut annuler une distribution encore en attente.
     *
     * @response {"data":{"id":"uuid","statut":"rejetee"},"message":"Distribution annulée."}
     * @response 422 {"message":"Seule une distribution en attente peut être annulée.","errors":{"statut":["Seule une distribution en attente peut être annulée."]}}
     */
    public function annuler(Request $request, string $id): JsonResponse
    {
        $distribution = $this->service->rejeter($id, $request->user()->id);

        return response()->json([
            'data'    => new DistributionResource($distribution),
            'message' => 'Distribution annulée.',
        ]);
    }
}
