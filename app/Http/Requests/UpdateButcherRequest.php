<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateButcherRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'sometimes|required|string|max:100',
            'address' => 'sometimes|required|string|max:255',
            'city' => 'sometimes|required|string|max:100',
            'postal_code' => 'sometimes|required|string|max:10',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:100',
            'opening_hours' => 'nullable|string|max:255',
            'website' => 'nullable|string|max:100',
            'owner' => 'nullable|string|max:100',
            'specialties' => 'nullable|string|max:255',
            'average_rating' => 'nullable|numeric',
            'review_count' => 'nullable|integer'
        ];
    }
}
