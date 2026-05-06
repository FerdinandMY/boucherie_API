<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateLivraisonRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'adresse_livraison' => ['sometimes', 'string', 'max:500'],
            'statut'            => ['sometimes', 'string', Rule::exists('enum_valeurs', 'valeur')->where('type', 'statut_livraison')],
            'date_prevue'       => ['nullable', 'date'],
        ];
    }
}
