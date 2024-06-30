<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Symfony\Component\HttpFoundation\Response;

class StoreTypeStockRequest extends FormRequest
{
    public function authorize()
    {
        // Autorisez cette requête pour tous les utilisateurs
        return true;
    }

    public function rules()
    {
        return [
            'type_name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ];
    }

    public function messages()
    {
        return [
            'type_name.required' => 'Le type name est obligatoire.',
            'type_name.string' => 'Le type name doit être une chaîne de caractères.',
            'name.max' => 'Le nom ne peut pas dépasser 255 caractères.',
            'description.string' => 'La description doit être une chaîne de caractères.',
            'description.max' => 'La description ne peut pas dépasser 1000 caractères.',
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
