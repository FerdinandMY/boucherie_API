<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginUserRequest;
use App\Http\Requests\RegisterUserRequest;
use App\Http\Resources\UserResource;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;

/**
 * @group Authentification
 *
 * Inscription, connexion et déconnexion via Laravel Sanctum.
 * @unauthenticated
 */
class AuthController extends Controller
{
    public function __construct(private readonly AuthService $authService) {}

    /**
     * Inscription
     *
     * Crée un compte utilisateur et retourne un token Bearer.
     *
     * @response 201 {"data": {"id": 1, "name": "Alice", "email": "alice@example.com", "role": "boucher"}, "token": "1|abc...", "message": "Compte créé avec succès."}
     */
    public function register(RegisterUserRequest $request): JsonResponse
    {
        $result = $this->authService->register($request->validated());

        return response()->json([
            'data'    => new UserResource($result['user']),
            'message' => 'Compte créé avec succès.',
            'token'   => $result['token'],
        ], 201);
    }

    /**
     * Connexion
     *
     * Authentifie l'utilisateur et retourne un token Bearer Sanctum.
     *
     * @response {"data": {"id": 1, "name": "Alice", "email": "alice@example.com", "role": "admin"}, "token": "1|abc...", "message": "Connexion réussie."}
     */
    public function login(LoginUserRequest $request): JsonResponse
    {
        $result = $this->authService->login($request->validated());

        return response()->json([
            'data'    => new UserResource($result['user']),
            'message' => 'Connexion réussie.',
            'token'   => $result['token'],
        ]);
    }

    /**
     * Déconnexion
     *
     * Révoque tous les tokens de l'utilisateur courant.
     *
     * @authenticated
     * @response {"message": "Déconnexion réussie."}
     */
    public function logout(): JsonResponse
    {
        $this->authService->logout(auth()->user());

        return response()->json(['message' => 'Déconnexion réussie.']);
    }

    /**
     * Profil courant
     *
     * Retourne les informations de l'utilisateur authentifié.
     *
     * @authenticated
     */
    public function me(): UserResource
    {
        return new UserResource(auth()->user()->load('roles', 'boucherie'));
    }
}
