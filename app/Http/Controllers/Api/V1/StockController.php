<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\AjusterStockRequest;
use App\Http\Resources\MouvementStockResource;
use App\Http\Resources\StockResource;
use App\Services\StockService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * @group Stocks
 * @authenticated
 * Consultation et ajustement des stocks. Chaque ajustement génère un mouvement de stock tracé.
 */
class StockController extends Controller
{
    public function __construct(private readonly StockService $service) {}

    /**
     * Liste des stocks
     *
     * @response {"data":[{"id":1,"boucherie_id":1,"produit_id":1,"produit":null,"abattage_id":1,"quantite":45.5,"seuil_alerte":10,"en_alerte":false,"created_at":"2024-01-16T06:00:00.000Z","updated_at":"2024-01-16T06:00:00.000Z"}],"links":{"first":"http://localhost/api/v1/stocks?page=1","last":"http://localhost/api/v1/stocks?page=1","prev":null,"next":null},"meta":{"current_page":1,"from":1,"last_page":1,"path":"http://localhost/api/v1/stocks","per_page":15,"to":1,"total":1}}
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        return StockResource::collection(
            $this->service->paginate($request->user()->boucherie_id)
        );
    }

    /**
     * Détail d'un stock
     *
     * @response {"data":{"id":1,"boucherie_id":1,"produit_id":1,"produit":{"id":1,"boucherie_id":1,"nom":"Côte de bœuf","categorie":"boeuf","unite":"kg","prix_unitaire":2500,"description":"Côte à l'os maturée 21 jours","created_at":"2024-01-15T10:00:00.000Z","updated_at":"2024-01-15T10:00:00.000Z"},"abattage_id":1,"quantite":45.5,"seuil_alerte":10,"en_alerte":false,"created_at":"2024-01-16T06:00:00.000Z","updated_at":"2024-01-16T06:00:00.000Z"}}
     * @response 404 {"message":"Not found."}
     */
    public function show(string $id): JsonResponse
    {
        $stock = $this->service->getById($id);
        $this->authorize('view', $stock);

        return response()->json([
            'data' => new StockResource($stock),
        ]);
    }

    /**
     * Mouvements d'un stock
     *
     * Retourne l'historique paginé des mouvements (entrées, sorties, ajustements).
     *
     * @response {"data":[{"id":1,"stock_id":1,"user_id":1,"type":"sortie","quantite":5,"motif":"Vente #42","created_at":"2024-01-17T14:00:00.000Z"}],"links":{"first":"http://localhost/api/v1/stocks/1/mouvements?page=1","last":"http://localhost/api/v1/stocks/1/mouvements?page=1","prev":null,"next":null},"meta":{"current_page":1,"from":1,"last_page":1,"path":"http://localhost/api/v1/stocks/1/mouvements","per_page":15,"to":1,"total":1}}
     * @response 404 {"message":"Not found."}
     */
    public function mouvements(string $id): AnonymousResourceCollection
    {
        $stock = $this->service->getById($id);
        $this->authorize('view', $stock);

        return MouvementStockResource::collection($this->service->mouvements($id));
    }

    /**
     * Ajuster un stock
     *
     * Crée un mouvement de type `ajustement` et met à jour la quantité.
     *
     * @response {"data":{"id":1,"boucherie_id":1,"produit_id":1,"produit":null,"abattage_id":1,"quantite":50.5,"seuil_alerte":10,"en_alerte":false,"created_at":"2024-01-16T06:00:00.000Z","updated_at":"2024-01-17T14:00:00.000Z"},"message":"Stock ajusté avec succès."}
     * @response 404 {"message":"Not found."}
     * @response 422 {"message":"The quantite field is required.","errors":{"quantite":["The quantite field is required."]}}
     */
    public function ajuster(AjusterStockRequest $request, string $id): JsonResponse
    {
        $stock = $this->service->getById($id);
        $this->authorize('update', $stock);

        $stock = $this->service->ajuster($id, $request->validated(), $request->user()->id);

        return response()->json([
            'data'    => new StockResource($stock),
            'message' => 'Stock ajusté avec succès.',
        ]);
    }
}
