<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AjusterStockRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type'     => ['required', 'string', Rule::exists('enum_valeurs', 'valeur')->where('type', 'type_mouvement')],
            'quantite' => ['required', 'numeric', 'min:0.01'],
            'motif'    => ['nullable', 'string', 'max:500'],
        ];
    }
}
