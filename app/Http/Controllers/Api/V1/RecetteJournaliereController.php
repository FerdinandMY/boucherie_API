<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRecetteJournaliereRequest;
use App\Http\Resources\RecetteJournaliereResource;
use App\Models\Fournisseur;
use App\Services\RecetteJournaliereService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * @group Recettes journalières (v2)
 * @authenticated
 * Enregistrement journalier des ventes et versements fournisseur.
 */
class RecetteJournaliereController extends Controller
{
    public function __construct(private readonly RecetteJournaliereService $service) {}

    /**
     * Liste des recettes
     *
     * @queryParam page integer Numéro de page. Example: 1
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $user = $request->user();

        $paginator = match (true) {
            $user->hasRole('admin')       => $this->service->paginateAll(),
            $user->hasRole('fournisseur') => $this->service->paginateByFournisseur(
                $user->fournisseur?->id ?? abort(422, 'Profil fournisseur introuvable.')
            ),
            default => $this->service->paginateByBoucherie($user->boucherie_id),
        };

        return RecetteJournaliereResource::collection($paginator);
    }

    /**
     * Enregistrer la recette du jour
     *
     * La boucherie saisit les ventes par catégorie et le montant versé au fournisseur.
     *
     * @response 201 {"data":{"id":"uuid","date":"2026-05-16","montant_total":150000,"montant_verse":100000,"statut_versement":"en_attente","lignes":[{"categorie":"viande_rouge","poids_kg_vendu":50,"prix_par_kg":2500,"montant":125000}]},"message":"Recette enregistrée avec succès."}
     * @response 422 {"message":"The lignes field is required.","errors":{"lignes":["The lignes field is required."]}}
     */
    public function store(StoreRecetteJournaliereRequest $request): JsonResponse
    {
        $user        = $request->user();
        $boucherieId = $user->boucherie_id;

        // Résoudre le fournisseur lié à cette boucherie
        $fournisseur = Fournisseur::whereHas('achats', fn ($q) =>
            $q->whereHas('animal.abattage.distributions', fn ($q2) =>
                $q2->where('boucherie_id', $boucherieId)
            )
        )->latest()->first();

        // Si aucun fournisseur trouvé via distributions, on attend que le boucher passe fournisseur_id
        $fournisseurId = $request->input('fournisseur_id') ?? $fournisseur?->id;

        if (! $fournisseurId) {
            abort(422, 'Impossible de déterminer le fournisseur. Passez fournisseur_id.');
        }

        $recette = $this->service->create(
            $request->validated(),
            $boucherieId,
            $fournisseurId
        );

        return response()->json([
            'data'    => new RecetteJournaliereResource($recette),
            'message' => 'Recette enregistrée avec succès.',
        ], 201);
    }

    /**
     * Détail d'une recette
     */
    public function show(string $id): JsonResponse
    {
        $recette = $this->service->findById($id);

        return response()->json([
            'data' => new RecetteJournaliereResource($recette),
        ]);
    }

    /**
     * Valider le versement
     *
     * Accessible au fournisseur uniquement.
     *
     * @response {"data":{...},"message":"Versement validé."}
     */
    public function valider(Request $request, string $id): JsonResponse
    {
        $fournisseurId = $request->user()->fournisseur?->id
            ?? abort(422, 'Profil fournisseur introuvable.');

        $recette = $this->service->validerVersement($id, $fournisseurId);

        return response()->json([
            'data'    => new RecetteJournaliereResource($recette),
            'message' => 'Versement validé.',
        ]);
    }

    /**
     * Rejeter le versement
     *
     * Accessible au fournisseur uniquement.
     *
     * @response {"data":{...},"message":"Versement rejeté."}
     */
    public function rejeter(Request $request, string $id): JsonResponse
    {
        $fournisseurId = $request->user()->fournisseur?->id
            ?? abort(422, 'Profil fournisseur introuvable.');

        $recette = $this->service->rejeterVersement($id, $fournisseurId);

        return response()->json([
            'data'    => new RecetteJournaliereResource($recette),
            'message' => 'Versement rejeté.',
        ]);
    }
}
