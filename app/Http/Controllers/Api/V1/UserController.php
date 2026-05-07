<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * @group Utilisateurs
 * @authenticated
 * Gestion des comptes utilisateurs (admin uniquement).
 */
class UserController extends Controller
{
    public function __construct(private readonly UserService $service) {}

    /**
     * Liste des utilisateurs
     *
     * @response {"data":[{"id":1,"name":"Alice Martin","email":"alice@boucherie.com","role":"admin","roles":["admin"],"boucherie_id":1,"created_at":"2024-01-15T10:00:00.000Z"}],"links":{"first":"http://localhost/api/v1/users?page=1","last":"http://localhost/api/v1/users?page=1","prev":null,"next":null},"meta":{"current_page":1,"from":1,"last_page":1,"path":"http://localhost/api/v1/users","per_page":15,"to":1,"total":1}}
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        return UserResource::collection(
            $this->service->paginate($request->user()->boucherie_id)
        );
    }

    /**
     * Créer un utilisateur
     *
     * @response 201 {"data":{"id":2,"name":"Bob Dupont","email":"bob@boucherie.com","role":"boucher","roles":["boucher"],"boucherie_id":1,"created_at":"2024-01-15T10:00:00.000Z"},"message":"Utilisateur créé avec succès."}
     * @response 422 {"message":"The email has already been taken.","errors":{"email":["The email has already been taken."]}}
     */
    public function store(StoreUserRequest $request): JsonResponse
    {
        $user = $this->service->create($request->validated());

        return response()->json([
            'data'    => new UserResource($user),
            'message' => 'Utilisateur créé avec succès.',
        ], 201);
    }

    /**
     * Détail d'un utilisateur
     *
     * @response {"data":{"id":1,"name":"Alice Martin","email":"alice@boucherie.com","role":"admin","roles":["admin"],"boucherie_id":1,"created_at":"2024-01-15T10:00:00.000Z"}}
     * @response 404 {"message":"Not found."}
     */
    public function show(string $id): JsonResponse
    {
        return response()->json([
            'data' => new UserResource($this->service->findById($id)),
        ]);
    }

    /**
     * Mettre à jour un utilisateur
     *
     * @response {"data":{"id":1,"name":"Alice Martin","email":"alice@boucherie.com","role":"admin","roles":["admin"],"boucherie_id":1,"created_at":"2024-01-15T10:00:00.000Z"},"message":"Utilisateur mis à jour avec succès."}
     * @response 404 {"message":"Not found."}
     * @response 422 {"message":"The email has already been taken.","errors":{"email":["The email has already been taken."]}}
     */
    public function update(UpdateUserRequest $request, string $id): JsonResponse
    {
        $user = $this->service->update($id, $request->validated());

        return response()->json([
            'data'    => new UserResource($user),
            'message' => 'Utilisateur mis à jour avec succès.',
        ]);
    }

    /**
     * Supprimer un utilisateur
     *
     * @response 204 {}
     * @response 404 {"message":"Not found."}
     */
    public function destroy(string $id): JsonResponse
    {
        $this->service->delete($id);

        return response()->json(null, 204);
    }
}
