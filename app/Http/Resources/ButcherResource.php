<?php

<<<<<<< HEAD
declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
=======
namespace App\Http\Resources;

>>>>>>> 9d2db2735a7d527683fc4f186b022af6f39b7383
use Illuminate\Http\Resources\Json\JsonResource;

class ButcherResource extends JsonResource
{
<<<<<<< HEAD
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'name'           => $this->name,
            'address'        => $this->address,
            'city'           => $this->city,
            'postal_code'    => $this->postal_code,
            'phone'          => $this->phone,
            'email'          => $this->email,
            'opening_hours'  => $this->opening_hours,
            'website'        => $this->website,
            'owner'          => $this->owner,
            'specialties'    => $this->specialties,
            'average_rating' => $this->average_rating,
            'review_count'   => $this->review_count,
            'created_at'     => $this->created_at?->toISOString(),
            'updated_at'     => $this->updated_at?->toISOString(),
        ];
    }
}
=======
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'address' => $this->address,
            'city' => $this->city,
            'postal_code' => $this->postal_code,
            'phone' => $this->phone,
            'email' => $this->email,
            'opening_hours' => $this->opening_hours,
            'website' => $this->website,
            'owner' => $this->owner,
            'specialties' => $this->specialties,
            'average_rating' => $this->average_rating,
            'review_count' => $this->review_count,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }

}


>>>>>>> 9d2db2735a7d527683fc4f186b022af6f39b7383
