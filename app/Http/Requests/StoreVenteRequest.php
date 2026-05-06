<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreVenteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type_vente'             => ['required', 'string', Rule::exists('enum_valeurs', 'valeur')->where('type', 'type_vente')],
            'client_id'              => ['nullable', 'uuid', 'exists:clients,id'],
            'notes'                  => ['nullable', 'string'],
            'lignes'                 => ['required', 'array', 'min:1'],
            'lignes.*.produit_id'    => ['required', 'uuid', 'exists:produits,id'],
            'lignes.*.quantite'      => ['required', 'numeric', 'min:0.01'],
            'lignes.*.prix_unitaire' => ['nullable', 'numeric', 'min:0'],
        ];
    }
}
