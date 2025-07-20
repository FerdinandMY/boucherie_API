<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'price' => 'nullable|numeric|min:0',
            'unit' => 'required|string|max:20',
            'reference' => 'nullable|string|max:50|unique:products,reference',
            'is_active' => 'boolean',
        ];
    }
}
