<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LivraisonResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                 => $this->id,
            'vente_id'           => $this->vente_id,
            'user_id'            => $this->user_id,
            'adresse_livraison'  => $this->adresse_livraison,
            'statut'             => $this->statut,
            'date_prevue'        => $this->date_prevue,
            'created_at'         => $this->created_at?->toISOString(),
            'updated_at'         => $this->updated_at?->toISOString(),
        ];
    }
}
