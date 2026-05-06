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

    public function index(Request $request): AnonymousResourceCollection
    {
        return UserResource::collection(
            $this->service->paginate($request->user()->boucherie_id)
        );
    }

    public function store(StoreUserRequest $request): JsonResponse
    {
        $user = $this->service->create($request->validated());

        return response()->json([
            'data'    => new UserResource($user),
            'message' => 'Utilisateur créé avec succès.',
        ], 201);
    }

    public function show(string $id): JsonResponse
    {
        return response()->json([
            'data' => new UserResource($this->service->findById($id)),
        ]);
    }

    public function update(UpdateUserRequest $request, string $id): JsonResponse
    {
        $user = $this->service->update($id, $request->validated());

        return response()->json([
            'data'    => new UserResource($user),
            'message' => 'Utilisateur mis à jour avec succès.',
        ]);
    }

    public function destroy(string $id): JsonResponse
    {
        $this->service->delete($id);

        return response()->json(null, 204);
    }
}
