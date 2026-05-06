<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AbattageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                 => $this->id,
            'boucherie_id'       => $this->boucherie_id,
            'animal_id'          => $this->animal_id,
            'animal'             => $this->whenLoaded('animal', fn () => new AnimalResource($this->animal)),
            'user_id'            => $this->user_id,
            'date_abattage'      => $this->date_abattage,
            'poids_carcasse_kg'  => $this->poids_carcasse_kg,
            'rendement_pct'      => $this->rendement_pct,
            'notes'              => $this->notes,
            'stocks'             => StockResource::collection($this->whenLoaded('stocks')),
            'created_at'         => $this->created_at?->toISOString(),
            'updated_at'         => $this->updated_at?->toISOString(),
        ];
    }
}
