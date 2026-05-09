<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @responseField id integer ID du paiement.
 * @responseField vente_id integer ID de la vente associée.
 * @responseField user_id integer ID du boucher ayant enregistré le paiement.
 * @responseField mode_paiement string Mode : especes, mobile_money, carte.
 * @responseField montant number Montant payé en FCFA.
 * @responseField date_paiement string Date du paiement (YYYY-MM-DD).
 * @responseField created_at string Date de création (ISO 8601).
 */
class PaiementResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'vente_id'       => $this->vente_id,
            'user_id'        => $this->user_id,
            'mode_paiement'  => $this->mode_paiement,
            'montant'        => $this->montant,
            'date_paiement'  => $this->date_paiement,
            'created_at'     => $this->created_at?->toISOString(),
        ];
    }
}
