<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreVersementRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'fournisseur_user_id' => ['required', 'integer', 'exists:users,id'],
            'montant'             => ['required', 'numeric', 'min:1'],
            'mode_paiement'       => ['required', 'string', 'max:50'],
            'date_versement'      => ['required', 'date'],
            'reference'           => ['nullable', 'string', 'max:100'],
            'notes'               => ['nullable', 'string', 'max:1000'],
        ];
    }
}
