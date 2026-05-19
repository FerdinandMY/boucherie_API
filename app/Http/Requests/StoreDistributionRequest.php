<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDistributionRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'abattage_id'          => ['required', 'uuid', 'exists:abattages,id'],
            'boucherie_id'         => ['required', 'uuid', 'exists:boucheries,id'],
            'produit_id'           => ['required', 'uuid', 'exists:produits,id'],
            'quantite'             => ['required', 'numeric', 'min:0.001'],
            'notes'                => ['nullable', 'string', 'max:1000'],
            'lignes'               => ['nullable', 'array'],
            'lignes.*.categorie'   => ['required_with:lignes', 'string', 'max:100'],
            'lignes.*.poids_kg'    => ['required_with:lignes', 'numeric', 'min:0'],
            'lignes.*.prix_par_kg' => ['nullable', 'numeric', 'min:0'],
        ];
    }
}
