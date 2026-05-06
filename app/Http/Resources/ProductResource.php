<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id'         => $this->id,
            'name'       => $this->name,
            'description'=> $this->description,
            'price'      => $this->price,
            'unit'       => $this->unit,
            'reference'  => $this->reference,
            'is_active'  => $this->is_active,
            'category'   => $this->category ? $this->category->name : null,
            'created_at' => $this->created_at,
        ];
    }
}

