<?php

namespace App\Http\Requests;

namespace App\Http\Requests;

class UpdateCategoryRequest extends StoreCategoryRequest
{
    public function rules(): array
    {
        $rules = parent::rules();
        $rules['name'] = 'required|string|max:100|unique:categories,name,' . $this->category->id;
        return $rules;
    }
}

