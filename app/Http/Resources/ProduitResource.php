<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

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
