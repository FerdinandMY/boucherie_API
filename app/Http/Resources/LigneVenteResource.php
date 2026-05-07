<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @responseField id integer ID de la ligne.
 * @responseField vente_id integer ID de la vente.
 * @responseField produit_id integer ID du produit.
 * @responseField produit object Détail du produit (si chargé).
 * @responseField quantite number Quantité vendue.
 * @responseField prix_unitaire number Prix unitaire au moment de la vente en FCFA.
 * @responseField sous_total number Sous-total de la ligne (quantite × prix_unitaire).
 */
class LigneVenteResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'vente_id'      => $this->vente_id,
            'produit_id'    => $this->produit_id,
            'produit'       => $this->whenLoaded('produit', fn () => new ProduitResource($this->produit)),
            'quantite'      => $this->quantite,
            'prix_unitaire' => $this->prix_unitaire,
            'sous_total'    => $this->sous_total,
        ];
    }
}
