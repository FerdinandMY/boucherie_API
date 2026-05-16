<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAbattageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // Fournisseur : le stock est alimenté plus tard via la réception
        // Boucher/Admin : le stock est créé directement à l'abattage
        $isFournisseur = $this->user()?->hasRole('fournisseur');

        return [
            'animal_id'         => ['required', 'uuid', 'exists:animaux,id'],
            'date_abattage'     => ['required', 'date'],
            'poids_carcasse_kg' => ['required', 'numeric', 'min:0'],
            'rendement_pct'     => ['nullable', 'numeric', 'min:0', 'max:100'],
            'notes'             => ['nullable', 'string'],

            'stocks'                 => [Rule::requiredIf(! $isFournisseur), 'nullable', 'array', 'min:1'],
            'stocks.*.produit_id'    => ['required_with:stocks', 'uuid', 'exists:produits,id'],
            'stocks.*.quantite'      => ['required_with:stocks', 'numeric', 'min:0'],
            'stocks.*.seuil_alerte'  => ['nullable', 'numeric', 'min:0'],
        ];
    }
}
