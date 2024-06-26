<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Symfony\Component\HttpFoundation\Response;

class CreateButcherRequest extends FormRequest
{
    /*public function authorize()
    {
        return true;
    }*/

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
            'website' => 'nullable|url|max:100',
            'owner' => 'nullable|string|max:100',
            'specialties' => 'nullable|string|max:255',
            'average_rating' => 'nullable|numeric|min:0|max:5',
            'review_count' => 'nullable|integer|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'name is required!',
            'name.max:100' => 'name is required!',
            'name.string' => 'name is required!',
            'address.required' => 'address is required!',
            'address.max:100' => 'address is required!',
            'address.string' => 'address is required!',
            'city.required' => 'city is required!',
            'city.max:100' => 'city is required!',
            'city.string' => 'city is required!',
            'postal_code.max:100' => 'postal_code is required!',
            'postal_code.string' => 'postal_code is required!',
            'phone.max:100' => 'phone is required!',
            'phone.string' => 'phone is required!',
            'email.max:100' => 'email is required!',
            'email.email' => 'email is required!',
        ];
    }

    public function failedValidation(Validator $validator)
    {

        throw new HttpResponseException(response()->json([
            'status' => Response::HTTP_INTERNAL_SERVER_ERROR,
            'success'   => false,
            'message'   => 'Validation errors',
            'data'      => [
                'responseMessage'=>"Donnée manquante durant la connexion",
                'responseMessageDev'=>$validator->errors(),
            ]
        ]));

    }
}
