<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;


class StoreStockRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'product_id' => 'required|integer|exists:products,id',
            'type_stock_id' => 'required|integer|exists:type_stocks,id',
            'quantity' => 'required|numeric|min:0',
            'unit' => 'required|string|max:20',
            'movement_type' => 'required|in:entrée,sortie,ajustement',
            'source_type' => 'nullable|string|max:255',
            'source_id' => 'nullable|integer|min:1',
        ];
    }
}

