<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @responseField id integer ID de l'achat.
 * @responseField boucherie_id integer ID de la boucherie.
 * @responseField fournisseur_id integer ID du fournisseur.
 * @responseField fournisseur object Détail du fournisseur (si chargé).
 * @responseField user_id integer ID de l'utilisateur ayant enregistré l'achat.
 * @responseField date_achat string Date de l'achat (YYYY-MM-DD).
 * @responseField montant_total number Montant total en FCFA.
 * @responseField notes string Notes libres.
 * @responseField animaux object[] Liste des animaux achetés (si chargée).
 * @responseField created_at string Date de création (ISO 8601).
 * @responseField updated_at string Date de modification (ISO 8601).
 */
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
