<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AchatFournisseurResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'boucherie_id'   => $this->boucherie_id,
            'fournisseur_id' => $this->fournisseur_id,
            'fournisseur'    => $this->whenLoaded('fournisseur', fn () => new FournisseurResource($this->fournisseur)),
            'user_id'        => $this->user_id,
            'date_achat'     => $this->date_achat,
            'montant_total'  => $this->montant_total,
            'notes'          => $this->notes,
            'animaux'        => AnimalResource::collection($this->whenLoaded('animaux')),
            'created_at'     => $this->created_at?->toISOString(),
            'updated_at'     => $this->updated_at?->toISOString(),
        ];
    }
}
