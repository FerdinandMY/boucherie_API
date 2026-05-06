<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProduitRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nom'           => ['sometimes', 'string', 'max:255'],
            'categorie'     => ['sometimes', 'string', Rule::exists('enum_valeurs', 'valeur')->where('type', 'categorie_produit')],
            'unite'         => ['sometimes', 'string', Rule::exists('enum_valeurs', 'valeur')->where('type', 'unite_produit')],
            'prix_unitaire' => ['sometimes', 'numeric', 'min:0'],
            'description'   => ['nullable', 'string'],
        ];
    }
}
