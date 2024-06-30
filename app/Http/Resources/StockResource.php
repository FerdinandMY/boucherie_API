<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StockResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id ?? null,
            'product_name' => $this->product_name ?? null,
            'type' => $this->type ?? null,
            'quantity' => $this->quantity ?? null,
            'unit' => $this->unit ?? null,
            'price_per_unit' => $this->price_per_unit ?? null,
            'butchers' => ButcherShopResource::collection($this->whenLoaded('butchers')),
        ];
    }
}
