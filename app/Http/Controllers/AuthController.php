<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginUserRequest;
use App\Http\Requests\RegisterUserRequest;
use Exception;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Validator;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }

    public function register(RegisterUserRequest $registerUserRequest): \Illuminate\Http\JsonResponse
    {
        try {
                $user = new User([
                    'name'  => $registerUserRequest->name ?? '',
                    'email' => $registerUserRequest->email ?? '',
                    'password' => bcrypt($registerUserRequest->password ?? ''),
                ]);

            if($user->save()){
                $tokenResult = $user->createToken('Personal Access Token');
                $token = $tokenResult->plainTextToken;

                return response()->json([
                    'message' => 'Successfully created user!',
                    'accessToken'=> $token,
                ],201);
            }
            else{
                return response()->json(['message'=>'Provide proper details'], 400);
            }
        } catch (Exception $exception) {
            return response()->json(["message" => $exception->getMessage()], 400);
        }
    }

    /**
     * Login user and create token
     *
     * @param  [string] email
     * @param  [string] password
     * @param  [boolean] remember_me
     */

    public function login(LoginUserRequest $loginUserRequest): \Illuminate\Http\JsonResponse
    {

        try {

            $credentials = request(['email','password']);
            if(!Auth::attempt($credentials))
            {
                return response()->json([
                    'message' => 'Unauthorized'
                ],401);
            }

            $user = $loginUserRequest->user();
            $tokenResult = $user->createToken('Personal Access Token');
            $token = $tokenResult->plainTextToken;

            return response()->json([
                'accessToken' =>$token,
                'token_type' => 'Bearer',
            ],200);

        }catch (Exception $exception){
            return response()->json(["message" => $exception->getMessage()], 400);
        }
    }

    /**
     * Get the authenticated User
     *
     * @return [json] user object
     */
    public function user(Request $request): \Illuminate\Http\JsonResponse
    {
        return response()->json($request->user());
    }

    /**
     * Logout user (Revoke the token)
     *
     * @return [string] message
     */
    public function logout(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'message' => 'Successfully logged out'
        ]);

    }

}
