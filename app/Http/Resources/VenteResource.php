<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @responseField id integer ID de la vente.
 * @responseField boucherie_id integer ID de la boucherie.
 * @responseField user_id integer ID du vendeur.
 * @responseField client_id integer ID du client.
 * @responseField client object Détail du client (si chargé).
 * @responseField type_vente string Type de vente : comptoir, livraison.
 * @responseField statut string Statut : en_attente, payee, annulee.
 * @responseField montant_total number Montant total en FCFA.
 * @responseField notes string Notes libres.
 * @responseField lignes object[] Lignes de vente (si chargées).
 * @responseField paiements object[] Paiements enregistrés (si chargés).
 * @responseField livraison object Livraison associée (si chargée).
 * @responseField created_at string Date de création (ISO 8601).
 * @responseField updated_at string Date de modification (ISO 8601).
 */
class VenteResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'boucherie_id'   => $this->boucherie_id,
            'user_id'        => $this->user_id,
            'client_id'      => $this->client_id,
            'client'         => $this->whenLoaded('client', fn () => new ClientResource($this->client)),
            'type_vente'     => $this->type_vente,
            'statut'         => $this->statut,
            'montant_total'  => $this->montant_total,
            'notes'          => $this->notes,
            'lignes'         => LigneVenteResource::collection($this->whenLoaded('lignes')),
            'paiements'      => PaiementResource::collection($this->whenLoaded('paiements')),
            'livraison'      => $this->whenLoaded('livraison', fn () => new LivraisonResource($this->livraison)),
            'attachments'    => AttachmentResource::collection($this->whenLoaded('attachments')),
            'created_at'     => $this->created_at?->toISOString(),
            'updated_at'     => $this->updated_at?->toISOString(),
        ];
    }
}
