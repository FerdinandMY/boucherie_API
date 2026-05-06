<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VenteResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'boucherie_id'   => $this->boucherie_id,
            'user_id'        => $this->user_id,
            'client_id'      => $this->client_id,
            'client'         => $this->whenLoaded('client', fn () => new ClientResource($this->client)),
            'type_vente'     => $this->type_vente,
            'statut'         => $this->statut,
            'montant_total'  => $this->montant_total,
            'notes'          => $this->notes,
            'lignes'         => LigneVenteResource::collection($this->whenLoaded('lignes')),
            'paiements'      => PaiementResource::collection($this->whenLoaded('paiements')),
            'livraison'      => $this->whenLoaded('livraison', fn () => new LivraisonResource($this->livraison)),
            'created_at'     => $this->created_at?->toISOString(),
            'updated_at'     => $this->updated_at?->toISOString(),
        ];
    }
}
