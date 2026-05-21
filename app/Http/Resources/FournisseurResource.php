<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @responseField id integer ID du fournisseur.
 * @responseField boucherie_id integer ID de la boucherie.
 * @responseField nom string Nom du fournisseur.
 * @responseField contact string Personne de contact.
 * @responseField email string Adresse e-mail.
 * @responseField telephone string Numéro de téléphone.
 * @responseField adresse string Adresse postale.
 * @responseField created_at string Date de création (ISO 8601).
 * @responseField updated_at string Date de modification (ISO 8601).
 */
class FournisseurResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'user_id'      => $this->user_id,
            'boucherie_id' => $this->boucherie_id,
            'nom'          => $this->nom,
            'contact'      => $this->contact,
            'email'        => $this->email,
            'telephone'    => $this->telephone,
            'adresse'      => $this->adresse,
            'created_at'   => $this->created_at?->toISOString(),
            'updated_at'   => $this->updated_at?->toISOString(),
        ];
    }
}
