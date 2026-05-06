<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProduitRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nom'           => ['required', 'string', 'max:255'],
            'categorie'     => ['required', 'string', Rule::exists('enum_valeurs', 'valeur')->where('type', 'categorie_produit')],
            'unite'         => ['required', 'string', Rule::exists('enum_valeurs', 'valeur')->where('type', 'unite_produit')],
            'prix_unitaire' => ['required', 'numeric', 'min:0'],
            'description'   => ['nullable', 'string'],
        ];
    }
}
