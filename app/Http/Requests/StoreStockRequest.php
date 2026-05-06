<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreStockRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'butcher_id'      => ['required', 'integer', 'exists:butchers,id'],
            'product_name'    => ['required', 'string', 'max:100'],
            'quantity'        => ['required', 'integer', 'min:0'],
            'quantity_meat'   => ['required', 'integer', 'min:0'],
            'quantity_tripe'  => ['required', 'integer', 'min:0'],
            'unit'            => ['required', 'string', 'max:20'],
            'date_added'      => ['required', 'date'],
            'expiration_date' => ['nullable', 'date', 'after:date_added'],
            'price_per_unit'  => ['required', 'numeric', 'min:0'],
            'supplier'        => ['nullable', 'string', 'max:100'],
            'remarks'         => ['nullable', 'string'],
        ];
    }
}
