<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @responseField id string ID du versement.
 * @responseField boucherie_id string ID de la boucherie qui verse.
 * @responseField boucherie object Boucherie (si chargée).
 * @responseField fournisseur_user_id integer ID de l'utilisateur fournisseur destinataire.
 * @responseField montant number Montant versé en FCFA.
 * @responseField mode_paiement string Mode : especes, mobile_money, virement, cheque.
 * @responseField date_versement string Date du versement (YYYY-MM-DD).
 * @responseField reference string Référence bancaire ou numéro de transaction.
 * @responseField statut string Statut : en_attente, valide, rejete.
 * @responseField motif_rejet string Motif en cas de rejet (null sinon).
 * @responseField notes string Notes libres.
 * @responseField valide_par integer ID de l'utilisateur ayant validé/rejeté.
 * @responseField valide_le string Date de validation/rejet (ISO 8601).
 * @responseField created_at string Date de création (ISO 8601).
 * @responseField updated_at string Date de modification (ISO 8601).
 */
class VersementResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                  => $this->id,
            'boucherie_id'        => $this->boucherie_id,
            'boucherie'           => $this->whenLoaded('boucherie', fn () => new BoucherieResource($this->boucherie)),
            'fournisseur_user_id' => $this->fournisseur_user_id,
            'montant'             => $this->montant,
            'mode_paiement'       => $this->mode_paiement,
            'date_versement'      => $this->date_versement?->toDateString(),
            'reference'           => $this->reference,
            'statut'              => $this->statut,
            'motif_rejet'         => $this->motif_rejet,
            'notes'               => $this->notes,
            'valide_par'          => $this->valide_par,
            'valide_le'           => $this->valide_le?->toISOString(),
            'attachments'         => AttachmentResource::collection($this->whenLoaded('attachments')),
            'created_at'          => $this->created_at?->toISOString(),
            'updated_at'          => $this->updated_at?->toISOString(),
        ];
    }
}
