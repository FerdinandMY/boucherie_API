<?php

declare(strict_types=1);

namespace App\Exceptions;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;

class Handler extends ExceptionHandler
{
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    public function register(): void
    {
        $this->renderable(function (ModelNotFoundException $e) {
            return response()->json(['message' => 'Ressource introuvable.'], 404);
        });

        $this->renderable(function (AuthorizationException $e) {
            return response()->json(['message' => 'Action non autorisée.'], 403);
        });

        $this->renderable(function (AuthenticationException $e) {
            return response()->json(['message' => 'Non authentifié.'], 401);
        });

        $this->renderable(function (ValidationException $e) {
            return response()->json([
                'message' => 'Les données fournies sont invalides.',
                'errors'  => $e->errors(),
            ], 422);
        });
    }
}
