<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

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
