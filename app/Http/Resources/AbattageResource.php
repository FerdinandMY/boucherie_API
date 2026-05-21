<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @responseField id integer ID de l'abattage.
 * @responseField boucherie_id integer ID de la boucherie.
 * @responseField animal_id integer ID de l'animal abattu.
 * @responseField animal object Détail de l'animal (si chargé).
 * @responseField user_id integer ID de l'utilisateur ayant enregistré l'abattage.
 * @responseField date_abattage string Date d'abattage (YYYY-MM-DD).
 * @responseField poids_carcasse_kg number Poids carcasse en kilogrammes.
 * @responseField rendement_pct number Rendement carcasse en pourcentage.
 * @responseField notes string Notes libres.
 * @responseField stocks object[] Stocks générés par l'abattage (si chargés).
 * @responseField created_at string Date de création (ISO 8601).
 * @responseField updated_at string Date de modification (ISO 8601).
 */
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
            'distributions'    => DistributionResource::collection($this->whenLoaded('distributions')),
            'lignes'             => $this->whenLoaded('lignes'),
            'attachments'        => AttachmentResource::collection($this->whenLoaded('attachments')),
            'created_at'         => $this->created_at?->toISOString(),
            'updated_at'         => $this->updated_at?->toISOString(),
        ];
    }
}
