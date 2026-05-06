<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'name'         => $this->name,
            'email'        => $this->email,
            'roles'        => $this->whenLoaded('roles', fn () => $this->roles->pluck('name')),
            'role'         => $this->whenLoaded('roles', fn () => $this->roles->first()?->name),
            'boucherie_id' => $this->boucherie_id,
            'boucherie'    => $this->whenLoaded('boucherie', fn () => new BoucherieResource($this->boucherie)),
            'created_at'   => $this->created_at?->toISOString(),
        ];
    }
}
