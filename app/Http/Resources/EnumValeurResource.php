<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EnumValeurResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'type'         => $this->type,
            'valeur'       => $this->valeur,
            'libelle'      => $this->libelle,
            'systeme'      => $this->systeme,
            'boucherie_id' => $this->boucherie_id,
            'ordre'        => $this->ordre,
        ];
    }
}
