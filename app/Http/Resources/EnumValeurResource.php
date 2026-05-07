<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @responseField id integer ID de la valeur.
 * @responseField type string Type d'énumération (ex: espece_animal, statut_vente).
 * @responseField valeur string Valeur technique utilisée dans l'API.
 * @responseField libelle string Libellé affiché à l'utilisateur.
 * @responseField systeme boolean Vrai si la valeur est définie par le système (non modifiable).
 * @responseField boucherie_id integer ID de la boucherie (null si valeur système).
 * @responseField ordre integer Ordre d'affichage dans les listes.
 */
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
