<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @responseField id integer ID du mouvement.
 * @responseField stock_id integer ID du stock concerné.
 * @responseField user_id integer ID de l'utilisateur ayant effectué l'opération.
 * @responseField type string Type de mouvement : entree, sortie, ajustement.
 * @responseField quantite number Quantité déplacée.
 * @responseField motif string Motif du mouvement.
 * @responseField created_at string Date du mouvement (ISO 8601).
 */
class MouvementStockResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'stock_id'   => $this->stock_id,
            'user_id'    => $this->user_id,
            'type'       => $this->type,
            'quantite'   => $this->quantite,
            'motif'      => $this->motif,
            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}
