<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Concerns\LinksAttachments;
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
    use LinksAttachments;

    public function __construct(private readonly VenteService $service) {}

    /**
     * Liste des ventes
     *
     * Filtrable via `?statut=`, `?type_vente=`, `?date_debut=`, `?date_fin=`.
     *
     * @queryParam statut string Filtre par statut. Exemple: en_attente
     * @queryParam type_vente string Filtre par type. Exemple: comptoir
     * @queryParam date_debut string Date de début (YYYY-MM-DD). Exemple: 2024-01-01
     * @queryParam date_fin string Date de fin (YYYY-MM-DD). Exemple: 2024-01-31
     * @response {"data":[{"id":1,"boucherie_id":1,"user_id":1,"client_id":1,"client":null,"type_vente":"comptoir","statut":"en_attente","montant_total":12500,"notes":null,"lignes":[],"paiements":[],"livraison":null,"created_at":"2024-01-17T14:00:00.000Z","updated_at":"2024-01-17T14:00:00.000Z"}],"links":{"first":"http://localhost/api/v1/ventes?page=1","last":"http://localhost/api/v1/ventes?page=1","prev":null,"next":null},"meta":{"current_page":1,"from":1,"last_page":1,"path":"http://localhost/api/v1/ventes","per_page":15,"to":1,"total":1}}
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        return VenteResource::collection(
            $this->service->paginate(
                $request->user()->boucherie_id,
                $request->only('statut', 'type_vente', 'date_debut', 'date_fin')
            )
        );
    }

    /**
     * Créer une vente
     *
     * Décrémente automatiquement le stock pour chaque ligne de vente.
     *
     * @response 201 {"data":{"id":1,"boucherie_id":1,"user_id":1,"client_id":1,"client":null,"type_vente":"comptoir","statut":"en_attente","montant_total":12500,"notes":null,"lignes":[{"id":1,"vente_id":1,"produit_id":1,"produit":null,"quantite":5,"prix_unitaire":2500,"sous_total":12500}],"paiements":[],"livraison":null,"created_at":"2024-01-17T14:00:00.000Z","updated_at":"2024-01-17T14:00:00.000Z"},"message":"Vente créée avec succès."}
     * @response 422 {"message":"The lignes field is required.","errors":{"lignes":["The lignes field is required."]}}
     */
    public function store(StoreVenteRequest $request): JsonResponse
    {
        $data                 = $request->validated();
        $attachmentIds        = $this->stripAttachmentIds($data);
        $data['boucherie_id'] = $request->user()->boucherie_id;

        $vente = $this->linkAttachments(
            $this->service->create($data, $request->user()->id),
            $attachmentIds,
            $request->user(),
        );

        return response()->json([
            'data'    => new VenteResource($vente),
            'message' => 'Vente créée avec succès.',
        ], 201);
    }

    /**
     * Détail d'une vente
     *
     * @response {"data":{"id":1,"boucherie_id":1,"user_id":1,"client_id":1,"client":{"id":1,"boucherie_id":1,"nom":"Restaurant Le Bon Goût","telephone":"+33612345678","email":"contact@lebongout.fr","adresse":"5 avenue de la Gastronomie, 75002 Paris","created_at":"2024-01-15T10:00:00.000Z","updated_at":"2024-01-15T10:00:00.000Z"},"type_vente":"comptoir","statut":"en_attente","montant_total":12500,"notes":null,"lignes":[{"id":1,"vente_id":1,"produit_id":1,"produit":null,"quantite":5,"prix_unitaire":2500,"sous_total":12500}],"paiements":[],"livraison":null,"created_at":"2024-01-17T14:00:00.000Z","updated_at":"2024-01-17T14:00:00.000Z"}}
     * @response 404 {"message":"Not found."}
     */
    public function show(string $id): JsonResponse
    {
        $vente = $this->service->findById($id);
        $this->authorize('view', $vente);

        return response()->json([
            'data' => new VenteResource($vente),
        ]);
    }

    /**
     * Mettre à jour le statut d'une vente
     *
     * L'annulation (`annulee`) restitue le stock et génère un mouvement d'entrée.
     *
     * @response {"data":{"id":1,"boucherie_id":1,"user_id":1,"client_id":1,"client":null,"type_vente":"comptoir","statut":"payee","montant_total":12500,"notes":null,"lignes":[],"paiements":[],"livraison":null,"created_at":"2024-01-17T14:00:00.000Z","updated_at":"2024-01-17T15:00:00.000Z"},"message":"Statut mis à jour avec succès."}
     * @response 404 {"message":"Not found."}
     * @response 422 {"message":"The statut field is required.","errors":{"statut":["The statut field is required."]}}
     */
    public function updateStatut(UpdateVenteStatutRequest $request, string $id): JsonResponse
    {
        $vente = $this->service->findById($id);
        $this->authorize('update', $vente);

        $vente = $this->service->updateStatut($id, $request->validated(), $request->user()->id);

        return response()->json([
            'data'    => new VenteResource($vente),
            'message' => 'Statut mis à jour avec succès.',
        ]);
    }

    /**
     * Annuler / supprimer une vente
     *
     * @response 204 {}
     * @response 404 {"message":"Not found."}
     */
    public function destroy(string $id): JsonResponse
    {
        $vente = $this->service->findById($id);
        $this->authorize('delete', $vente);

        $this->service->delete($id);

        return response()->json(null, 204);
    }
}
