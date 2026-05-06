<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePaiementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'montant'       => ['required', 'numeric', 'min:0.01'],
            'mode_paiement' => ['required', 'string', Rule::exists('enum_valeurs', 'valeur')->where('type', 'mode_paiement')],
            'date_paiement' => ['nullable', 'date'],
        ];
    }
}
