<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateStockRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'product_name'    => ['sometimes', 'required', 'string', 'max:100'],
            'quantity'        => ['sometimes', 'required', 'integer', 'min:0'],
            'quantity_meat'   => ['sometimes', 'required', 'integer', 'min:0'],
            'quantity_tripe'  => ['sometimes', 'required', 'integer', 'min:0'],
            'unit'            => ['sometimes', 'required', 'string', 'max:20'],
            'date_added'      => ['sometimes', 'required', 'date'],
            'expiration_date' => ['nullable', 'date'],
            'price_per_unit'  => ['sometimes', 'required', 'numeric', 'min:0'],
            'supplier'        => ['nullable', 'string', 'max:100'],
            'remarks'         => ['nullable', 'string'],
        ];
    }
}
