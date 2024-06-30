<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Symfony\Component\HttpFoundation\Response;

class UpdateTypeStockRequest extends FormRequest
{

    public function authorize()
    {
        // Autorisez cette requête pour tous les utilisateurs
        return true;
    }

    public function rules()
    {
        return [
            'type_name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ];
    }

    public function messages()
    {
        return [
            'type_name.required' => 'Le nom est obligatoire.',
            'type_name.string' => 'Le nom doit être une chaîne de caractères.',
            'type_name.max' => 'Le nom ne peut pas dépasser 255 caractères.',
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
