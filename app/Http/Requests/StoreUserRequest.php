<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'role'     => ['required', 'string', Rule::in(['admin', 'boucher', 'fournisseur'])],

            // Obligatoire pour admin et boucher, interdit pour fournisseur
            'boucherie_id' => [
                Rule::requiredIf(fn () => $this->input('role') !== 'fournisseur'),
                'nullable',
                'uuid',
                'exists:boucheries,id',
            ],

            // Optionnel même pour le rôle fournisseur — l'admin peut compléter l'entité plus tard
            'fournisseur.nom'       => ['sometimes', 'nullable', 'string', 'max:150'],
            'fournisseur.contact'   => ['sometimes', 'nullable', 'string', 'max:150'],
            'fournisseur.telephone' => ['sometimes', 'nullable', 'string', 'max:20'],
            'fournisseur.email'     => ['sometimes', 'nullable', 'email', 'max:150'],
            'fournisseur.adresse'   => ['sometimes', 'nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'boucherie_id.required' => 'La boucherie est obligatoire pour ce rôle.',
        ];
    }
}
