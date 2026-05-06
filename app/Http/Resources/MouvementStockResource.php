<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MouvementStockResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'stock_id'   => $this->stock_id,
            'user_id'    => $this->user_id,
            'type'       => $this->type,
            'quantite'   => $this->quantite,
            'motif'      => $this->motif,
            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}
