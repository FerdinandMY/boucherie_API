<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'         => ['sometimes', 'string', 'max:255'],
            'email'        => ['sometimes', 'email', Rule::unique('users', 'email')->ignore($this->route('user'))],
            'password'     => ['sometimes', 'string', 'min:8'],
            'role'         => ['sometimes', 'string', Rule::in(['admin', 'boucher', 'caissier'])],
            'boucherie_id' => ['sometimes', 'uuid', 'exists:boucheries,id'],
        ];
    }
}
