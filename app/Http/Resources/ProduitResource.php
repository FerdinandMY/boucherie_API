<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @responseField id integer ID du produit.
 * @responseField boucherie_id integer ID de la boucherie.
 * @responseField nom string Nom du produit.
 * @responseField categorie string Catégorie (ex: boeuf, agneau, volaille).
 * @responseField unite string Unité de vente (ex: kg, piece).
 * @responseField prix_unitaire number Prix par unité en FCFA.
 * @responseField description string Description du produit.
 * @responseField created_at string Date de création (ISO 8601).
 * @responseField updated_at string Date de modification (ISO 8601).
 */
class ProduitResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'boucherie_id'  => $this->boucherie_id,
            'nom'           => $this->nom,
            'categorie'     => $this->categorie,
            'unite'         => $this->unite,
            'prix_unitaire' => $this->prix_unitaire,
            'description'   => $this->description,
            'created_at'    => $this->created_at?->toISOString(),
            'updated_at'    => $this->updated_at?->toISOString(),
        ];
    }
}
