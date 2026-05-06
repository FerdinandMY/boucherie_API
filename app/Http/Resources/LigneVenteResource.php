<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

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
