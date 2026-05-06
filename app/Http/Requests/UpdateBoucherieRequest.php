<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBoucherieRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nom'       => ['sometimes', 'string', 'max:255'],
            'adresse'   => ['nullable', 'string', 'max:500'],
            'ville'     => ['nullable', 'string', 'max:100'],
            'telephone' => ['nullable', 'string', 'max:20'],
            'actif'     => ['boolean'],
        ];
    }
}
