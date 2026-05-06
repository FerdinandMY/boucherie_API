<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AnimalResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                    => $this->id,
            'boucherie_id'          => $this->boucherie_id,
            'achat_fournisseur_id'  => $this->achat_fournisseur_id,
            'espece'                => $this->espece,
            'poids_vif_kg'          => $this->poids_vif_kg,
            'prix_achat'            => $this->prix_achat,
            'numero_tag'            => $this->numero_tag,
            'statut'                => $this->statut,
            'created_at'            => $this->created_at?->toISOString(),
            'updated_at'            => $this->updated_at?->toISOString(),
        ];
    }
}
