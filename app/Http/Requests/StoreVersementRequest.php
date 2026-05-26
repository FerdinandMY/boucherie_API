<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreVersementRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'fournisseur_user_id' => ['required', 'integer', 'exists:users,id'],
            'montant'             => ['required', 'numeric', 'min:1'],
            'mode_paiement'       => ['required', Rule::exists('enum_valeurs', 'valeur')->where('type', 'mode_paiement')],
            'date_versement'      => ['required', 'date'],
            'reference'           => ['nullable', 'string', 'max:100'],
            'notes'               => ['nullable', 'string', 'max:1000'],
            'attachment_ids'      => ['sometimes', 'array', 'max:3'],
            'attachment_ids.*'    => ['uuid', 'exists:attachments,id'],
        ];
    }
}
