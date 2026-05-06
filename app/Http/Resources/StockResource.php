<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StockResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
<<<<<<< HEAD
            'id'            => $this->id,
            'boucherie_id'  => $this->boucherie_id,
            'produit_id'    => $this->produit_id,
            'produit'       => $this->whenLoaded('produit', fn () => new ProduitResource($this->produit)),
            'abattage_id'   => $this->abattage_id,
            'quantite'      => $this->quantite,
            'seuil_alerte'  => $this->seuil_alerte,
            'en_alerte'     => $this->isEnAlerte(),
            'created_at'    => $this->created_at?->toISOString(),
            'updated_at'    => $this->updated_at?->toISOString(),
=======
            'id' => $this->id,
            'product_id' => $this->product_id,
            'type_stock_id' => $this->type_stock_id,
            'quantity' => $this->quantity,
            'unit' => $this->unit,
            'movement_type' => $this->movement_type,
            'source_type' => $this->source_type,
            'source_id' => $this->source_id,
            'created_at' => $this->created_at,
>>>>>>> 9d2db2735a7d527683fc4f186b022af6f39b7383
        ];
    }
}
