<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FournisseurResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'boucherie_id' => $this->boucherie_id,
            'nom'          => $this->nom,
            'contact'      => $this->contact,
            'email'        => $this->email,
            'telephone'    => $this->telephone,
            'adresse'      => $this->adresse,
            'created_at'   => $this->created_at?->toISOString(),
            'updated_at'   => $this->updated_at?->toISOString(),
        ];
    }
}
