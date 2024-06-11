<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Symfony\Component\HttpFoundation\Response;

class RegisterUserRequest extends FormRequest
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
            'name' => 'required|string',
            'email'=>'required|string|unique:users',
            'password'=>'required|string',
            'c_password' => 'required|same:password'
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'name is required!',
            'email.required' => 'email is required!',
            'password.required' => 'password is required!',
            'c_password.required' => 'c_password is required!',
            'c_password.same' => 'c_password must match!',
            'name.string' => 'name must be string!',
            'email.string' => 'email must be string!',
            'email.unique' => 'email must be unique!',
            'password.string' => 'password must be string!',
        ];
    }

    public function failedValidation(Validator $validator)
    {

        throw new HttpResponseException(response()->json([
            'status' => Response::HTTP_INTERNAL_SERVER_ERROR,
            'success'   => false,
            'message'   => 'Validation errors',
            'data'      => [
                'responseMessage'=>"Donnée manquante durant la création de compte",
                'responseMessageDev'=>$validator->errors(),
            ]
        ]));

    }
}
