<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RecetteJournaliereResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'               => $this->id,
            'boucherie_id'     => $this->boucherie_id,
            'boucherie'        => $this->whenLoaded('boucherie', fn () => [
                'id'  => $this->boucherie->id,
                'nom' => $this->boucherie->nom,
            ]),
            'fournisseur_id'   => $this->fournisseur_id,
            'fournisseur'      => $this->whenLoaded('fournisseur', fn () => [
                'id'  => $this->fournisseur->id,
                'nom' => $this->fournisseur->nom,
            ]),
            'date'             => $this->date?->toDateString(),
            'montant_total'    => $this->montant_total,
            'montant_verse'    => $this->montant_verse,
            'statut_versement' => $this->statut_versement,
            'notes'            => $this->notes,
            'lignes'           => $this->whenLoaded('lignes', fn () =>
                $this->lignes->map(fn ($l) => [
                    'id'             => $l->id,
                    'categorie'      => $l->categorie,
                    'poids_kg_vendu' => $l->poids_kg_vendu,
                    'prix_par_kg'    => $l->prix_par_kg,
                    'montant'        => $l->montant,
                ])
            ),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
