<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateFournisseurRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nom'          => ['sometimes', 'string', 'max:255'],
            'contact'      => ['sometimes', 'nullable', 'string', 'max:255'],
            'email'        => ['sometimes', 'nullable', 'email', 'max:255'],
            'telephone'    => ['sometimes', 'nullable', 'string', 'max:20'],
            'adresse'      => ['sometimes', 'nullable', 'string', 'max:500'],
            'boucherie_id' => ['sometimes', 'nullable', 'uuid', 'exists:boucheries,id'],
        ];
    }
}
