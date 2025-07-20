<?php

namespace App\Http\Requests;

class UpdateProductRequest extends StoreProductRequest
{
    public function rules(): array
    {
        $rules = parent::rules();
        $rules['reference'] = 'nullable|string|max:50|unique:products,reference,' . $this->product->id;
        return $rules;
    }
}

