<?php

namespace App\Http\Requests;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Symfony\Component\HttpFoundation\Response;

/**
 *
 */
class LoginUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */

    public function rules(): array
    {
        return [
            'email' => 'required|string',
            'password'=>'required|string',
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'email is required!',
            'password.required' => 'password is required!',
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
