<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEnumValeurRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'valeur'  => ['required', 'string', 'max:100', 'regex:/^[a-z0-9_]+$/'],
            'libelle' => ['required', 'string', 'max:150'],
            'ordre'   => ['nullable', 'integer', 'min:0'],
        ];
    }
}
