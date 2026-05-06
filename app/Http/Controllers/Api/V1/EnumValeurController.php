<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEnumValeurRequest;
use App\Http\Requests\UpdateEnumValeurRequest;
use App\Http\Resources\EnumValeurResource;
use App\Services\EnumValeurService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * @group Référentiels (Enums)
 *
 * Gestion des valeurs de référence (espèces, catégories, unités, modes de paiement, statuts…).
 * Les valeurs `systeme=true` peuvent être lues mais pas supprimées ni modifiées.
 */
class EnumValeurController extends Controller
{
    public function __construct(private readonly EnumValeurService $service) {}

    /**
     * Lister les valeurs d'un type
     *
     * Retourne toutes les valeurs globales + celles de la boucherie de l'utilisateur.
     *
     * @urlParam type string required Le type d'enum. Exemples : espece_animal, categorie_produit, unite_produit, mode_paiement, statut_animal, type_vente, statut_vente, statut_livraison, type_mouvement. Example: espece_animal
     */
    public function index(Request $request, string $type): AnonymousResourceCollection
    {
        return EnumValeurResource::collection(
            $this->service->listByType($type, $request->user()->boucherie_id)
        );
    }

    /**
     * Ajouter une valeur
     *
     * Ajoute une nouvelle valeur pour le type donné, rattachée à la boucherie de l'utilisateur.
     *
     * @urlParam type string required Le type d'enum. Example: espece_animal
     */
    public function store(StoreEnumValeurRequest $request, string $type): JsonResponse
    {
        $valeur = $this->service->create($type, $request->validated(), $request->user()->boucherie_id);

        return response()->json([
            'data'    => new EnumValeurResource($valeur),
            'message' => 'Valeur ajoutée avec succès.',
        ], 201);
    }

    /**
     * Modifier le libellé ou l'ordre d'une valeur
     *
     * Seuls `libelle` et `ordre` sont modifiables. Le champ `valeur` est immuable.
     *
     * @urlParam type string required Le type d'enum. Example: espece_animal
     * @urlParam id string required UUID de la valeur. Example: 9d4b2c1a-...
     */
    public function update(UpdateEnumValeurRequest $request, string $type, string $id): JsonResponse
    {
        $valeur = $this->service->update($id, $request->validated());

        return response()->json([
            'data'    => new EnumValeurResource($valeur),
            'message' => 'Valeur mise à jour avec succès.',
        ]);
    }

    /**
     * Supprimer une valeur
     *
     * Impossible de supprimer une valeur `systeme=true`.
     *
     * @urlParam type string required Le type d'enum. Example: espece_animal
     * @urlParam id string required UUID de la valeur. Example: 9d4b2c1a-...
     * @response 204 {}
     */
    public function destroy(string $type, string $id): JsonResponse
    {
        $this->service->delete($id);

        return response()->json(null, 204);
    }
}
