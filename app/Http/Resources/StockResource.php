<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @responseField id integer ID du stock.
 * @responseField boucherie_id integer ID de la boucherie.
 * @responseField produit_id integer ID du produit.
 * @responseField produit object Détail du produit (si chargé).
 * @responseField abattage_id integer ID de l'abattage source.
 * @responseField quantite number Quantité disponible en stock.
 * @responseField seuil_alerte number Seuil déclenchant l'alerte de stock bas.
 * @responseField en_alerte boolean Vrai si la quantité est en dessous du seuil.
 * @responseField created_at string Date de création (ISO 8601).
 * @responseField updated_at string Date de modification (ISO 8601).
 */
class StockResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'boucherie_id' => $this->boucherie_id,
            'produit_id'   => $this->produit_id,
            'produit'      => $this->whenLoaded('produit', fn () => new ProduitResource($this->produit)),
            'abattage_id'  => $this->abattage_id,
            'quantite'     => $this->quantite,
            'seuil_alerte' => $this->seuil_alerte,
            'en_alerte'    => $this->isEnAlerte(),
            'created_at'   => $this->created_at?->toISOString(),
            'updated_at'   => $this->updated_at?->toISOString(),
        ];
    }
}
