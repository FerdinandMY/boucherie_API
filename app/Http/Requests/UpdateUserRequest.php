<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'name'         => ['sometimes', 'string', 'max:255'],
            'email'        => ['sometimes', 'email', Rule::unique('users', 'email')->ignore($this->route('user'))],
            'password'     => ['sometimes', 'string', 'min:8'],
            'role'         => ['sometimes', 'string', Rule::in(['admin', 'boucher', 'fournisseur'])],
            'boucherie_id' => ['sometimes', 'nullable', 'uuid', 'exists:boucheries,id'],

            'fournisseur.nom'       => ['sometimes', 'string', 'max:150'],
            'fournisseur.contact'   => ['sometimes', 'nullable', 'string', 'max:150'],
            'fournisseur.telephone' => ['sometimes', 'nullable', 'string', 'max:20'],
            'fournisseur.email'     => ['sometimes', 'nullable', 'email', 'max:150'],
            'fournisseur.adresse'   => ['sometimes', 'nullable', 'string', 'max:500'],
        ];
    }
}
