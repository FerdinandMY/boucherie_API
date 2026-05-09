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

            // Obligatoire uniquement quand le rôle est fournisseur
            'fournisseur.nom'       => ['required_if:role,fournisseur', 'nullable', 'string', 'max:150'],
            'fournisseur.contact'   => ['nullable', 'string', 'max:150'],
            'fournisseur.telephone' => ['nullable', 'string', 'max:20'],
            'fournisseur.email'     => ['nullable', 'email', 'max:150'],
            'fournisseur.adresse'   => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'boucherie_id.required' => 'La boucherie est obligatoire pour ce rôle.',
            'fournisseur.nom.required_if' => 'Le nom de l\'entité fournisseur est obligatoire.',
        ];
    }
}
