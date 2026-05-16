<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreReceptionRequest;
use App\Http\Resources\ReceptionResource;
use App\Services\ReceptionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * @group Réceptions
 * @authenticated
 * La boucherie réceptionne un lot distribué par le fournisseur.
 * La réception alimente automatiquement le stock et crée un mouvement d'entrée.
 */
class ReceptionController extends Controller
{
    public function __construct(private readonly ReceptionService $service) {}

    /**
     * Liste des réceptions de la boucherie
     *
     * @response {"data":[{"id":"uuid","distribution_id":"uuid","boucherie_id":"uuid","user_id":2,"quantite_recue":"45.000","date_reception":"2024-01-16","notes":null,"created_at":"2024-01-16T09:00:00.000Z"}],"links":{"first":"http://localhost/api/v1/receptions?page=1","last":"http://localhost/api/v1/receptions?page=1","prev":null,"next":null},"meta":{"current_page":1,"from":1,"last_page":1,"path":"http://localhost/api/v1/receptions","per_page":15,"to":1,"total":1}}
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $user = $request->user();

        $paginator = match (true) {
            $user->hasRole('fournisseur') => $this->service->paginateByFournisseur($user->id),
            $user->hasRole('boucher')     => $this->service->paginateByBoucherie($user->boucherie_id),
            default                       => $this->service->paginateByBoucherie(), // admin : toutes
        };

        return ReceptionResource::collection($paginator);
    }

    /**
     * Réceptionner une distribution
     *
     * Confirme la réception du lot, met à jour le statut de la distribution en `acceptee`
     * et alimente le stock de la boucherie.
     *
     * @response 201 {"data":{"id":"uuid","distribution_id":"uuid","boucherie_id":"uuid","user_id":2,"quantite_recue":"43.500","date_reception":"2024-01-16","notes":"Légère différence de poids","created_at":"2024-01-16T09:00:00.000Z"},"message":"Réception enregistrée. Stock mis à jour."}
     * @response 422 {"message":"Cette distribution a déjà été réceptionnée ou rejetée.","errors":{"distribution_id":["Cette distribution a déjà été réceptionnée ou rejetée."]}}
     * @response 404 {"message":"Not found."}
     */
    public function store(StoreReceptionRequest $request): JsonResponse
    {
        $reception = $this->service->create(
            $request->validated(),
            $request->user()->boucherie_id,
            $request->user()->id,
        );

        return response()->json([
            'data'    => new ReceptionResource($reception),
            'message' => 'Réception enregistrée. Stock mis à jour.',
        ], 201);
    }

    /**
     * Détail d'une réception
     *
     * @response {"data":{"id":"uuid","distribution_id":"uuid","boucherie_id":"uuid","user_id":2,"quantite_recue":"45.000","date_reception":"2024-01-16","notes":null,"created_at":"2024-01-16T09:00:00.000Z"}}
     * @response 404 {"message":"Not found."}
     */
    public function show(Request $request, string $id): JsonResponse
    {
        $reception = $this->service->findById($id);
        $user      = $request->user();

        if ($user->hasRole('boucher') && $reception->boucherie_id !== $user->boucherie_id) {
            abort(403, 'Cette réception ne concerne pas votre boucherie.');
        }

        if ($user->hasRole('fournisseur') && $reception->distribution?->fournisseur_user_id !== $user->id) {
            abort(403, 'Cette réception ne concerne pas vos distributions.');
        }

        return response()->json([
            'data' => new ReceptionResource($reception),
        ]);
    }
}
