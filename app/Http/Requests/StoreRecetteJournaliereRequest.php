<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRecetteJournaliereRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'date'           => ['required', 'date'],
            'montant_verse'  => ['required', 'numeric', 'min:0'],
            'fournisseur_id' => ['sometimes', 'nullable', 'uuid', 'exists:fournisseurs,id'],
            'notes'          => ['nullable', 'string'],

            'lignes'                   => ['required', 'array', 'min:1'],
            'lignes.*.categorie'       => ['required', 'string', Rule::exists('enum_valeurs', 'valeur')->where('type', 'categorie_produit')],
            'lignes.*.poids_kg_vendu'  => ['required', 'numeric', 'min:0'],
            'lignes.*.prix_par_kg'     => ['required', 'numeric', 'min:0'],
        ];
    }
}
