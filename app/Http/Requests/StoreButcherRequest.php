<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreButcherRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:100',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:100',
            'postal_code' => 'required|string|max:10',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:100',
            'opening_hours' => 'nullable|string|max:255',
            'website' => 'nullable|url|max:100',
            'owner' => 'nullable|string|max:100',
            'specialties' => 'nullable|string|max:255',
            'average_rating' => 'nullable|numeric|between:0,5',
            'review_count' => 'nullable|integer|min:0',
        ];
    }
}

