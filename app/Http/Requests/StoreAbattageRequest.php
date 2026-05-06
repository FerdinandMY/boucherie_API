<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAbattageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'animal_id'          => ['required', 'uuid', 'exists:animaux,id'],
            'date_abattage'      => ['required', 'date'],
            'poids_carcasse_kg'  => ['required', 'numeric', 'min:0'],
            'rendement_pct'      => ['nullable', 'numeric', 'min:0', 'max:100'],
            'notes'              => ['nullable', 'string'],
            'stocks'             => ['required', 'array', 'min:1'],
            'stocks.*.produit_id'    => ['required', 'uuid', 'exists:produits,id'],
            'stocks.*.quantite'      => ['required', 'numeric', 'min:0'],
            'stocks.*.seuil_alerte'  => ['nullable', 'numeric', 'min:0'],
        ];
    }
}
