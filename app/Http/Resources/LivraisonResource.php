<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @responseField id integer ID de la livraison.
 * @responseField vente_id integer ID de la vente associée.
 * @responseField user_id integer ID de l'utilisateur ayant créé la livraison.
 * @responseField adresse_livraison string Adresse de livraison.
 * @responseField statut string Statut : en_attente, en_cours, livree.
 * @responseField date_prevue string Date prévue de livraison (YYYY-MM-DD).
 * @responseField created_at string Date de création (ISO 8601).
 * @responseField updated_at string Date de modification (ISO 8601).
 */
class LivraisonResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                 => $this->id,
            'vente_id'           => $this->vente_id,
            'user_id'            => $this->user_id,
            'adresse_livraison'  => $this->adresse_livraison,
            'statut'             => $this->statut,
            'date_prevue'        => $this->date_prevue,
            'created_at'         => $this->created_at?->toISOString(),
            'updated_at'         => $this->updated_at?->toISOString(),
        ];
    }
}
