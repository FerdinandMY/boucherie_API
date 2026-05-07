<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @responseField id string ID de la réception.
 * @responseField distribution_id string ID de la distribution réceptionnée.
 * @responseField distribution object Détail de la distribution (si chargée).
 * @responseField boucherie_id string ID de la boucherie réceptionnaire.
 * @responseField user_id integer ID de l'utilisateur ayant enregistré la réception.
 * @responseField quantite_recue number Quantité effectivement reçue.
 * @responseField date_reception string Date de réception (YYYY-MM-DD).
 * @responseField notes string Notes libres.
 * @responseField created_at string Date de création (ISO 8601).
 */
class ReceptionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,
            'distribution_id' => $this->distribution_id,
            'distribution'    => $this->whenLoaded('distribution', fn () => new DistributionResource($this->distribution)),
            'boucherie_id'    => $this->boucherie_id,
            'user_id'         => $this->user_id,
            'quantite_recue'  => $this->quantite_recue,
            'date_reception'  => $this->date_reception?->toDateString(),
            'notes'           => $this->notes,
            'created_at'      => $this->created_at?->toISOString(),
        ];
    }
}
