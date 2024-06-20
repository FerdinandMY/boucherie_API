<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateButcherRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:100',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:100',
            'postal_code' => 'required|string|max:10',
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
