<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAchatFournisseurRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $isFournisseur = $this->user()?->hasRole('fournisseur');

        return [
            // Obligatoire seulement pour boucher/admin ; le fournisseur est résolu depuis son profil
            'fournisseur_id' => [
                Rule::requiredIf(! $isFournisseur),
                'nullable',
                'uuid',
                'exists:fournisseurs,id',
            ],
            'date_achat'               => ['required', 'date'],
            'montant_total'            => ['required', 'numeric', 'min:0'],
            'notes'                    => ['nullable', 'string'],
            'animaux'                  => ['required', 'array', 'min:1'],
            'animaux.*.espece'         => ['required', 'string', Rule::exists('enum_valeurs', 'valeur')->where('type', 'espece_animal')],
            'animaux.*.poids_vif_kg'   => ['required', 'numeric', 'min:0'],
            'animaux.*.prix_achat'     => ['required', 'numeric', 'min:0'],
            'animaux.*.numero_tag'     => ['nullable', 'string', 'max:50'],
        ];
    }
}
