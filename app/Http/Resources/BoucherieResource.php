<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @responseField id integer ID de la boucherie.
 * @responseField nom string Nom de l'établissement.
 * @responseField adresse string Adresse postale.
 * @responseField ville string Ville.
 * @responseField telephone string Numéro de téléphone.
 * @responseField actif boolean Indique si la boucherie est active.
 * @responseField created_at string Date de création (ISO 8601).
 * @responseField updated_at string Date de dernière modification (ISO 8601).
 */
class BoucherieResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'nom'        => $this->nom,
            'adresse'    => $this->adresse,
            'ville'      => $this->ville,
            'telephone'  => $this->telephone,
            'actif'        => $this->actif,
            'fournisseur'  => $this->when(
                $this->relationLoaded('fournisseurAssigne') && $this->fournisseurAssigne->isNotEmpty(),
                fn () => new FournisseurResource($this->fournisseurAssigne->first()),
            ),
            'attachments'  => AttachmentResource::collection($this->whenLoaded('attachments')),
            'created_at'   => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
