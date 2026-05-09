<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @responseField id integer ID de l'utilisateur.
 * @responseField name string Nom complet.
 * @responseField email string Adresse e-mail.
 * @responseField role string Rôle principal (admin, boucher, fournisseur).
 * @responseField roles string[] Liste des rôles attribués.
 * @responseField boucherie_id string UUID de la boucherie rattachée (null pour fournisseur).
 * @responseField boucherie object Détail de la boucherie (si chargé).
 * @responseField entite_fournisseur object Entité fournisseur liée (si rôle = fournisseur).
 * @responseField created_at string Date de création (ISO 8601).
 */
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
            'boucherie_id'       => $this->boucherie_id,
            'boucherie'          => $this->whenLoaded('boucherie', fn () => new BoucherieResource($this->boucherie)),
            'entite_fournisseur' => $this->whenLoaded('fournisseur', fn () => new FournisseurResource($this->fournisseur)),
            'created_at'         => $this->created_at?->toISOString(),
        ];
    }
}
