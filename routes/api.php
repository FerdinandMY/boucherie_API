<?php

declare(strict_types=1);

use App\Http\Controllers\Api\V1\AbattageController;
use App\Http\Controllers\Api\V1\AchatFournisseurController;
use App\Http\Controllers\Api\V1\AnimalController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\BoucherieController;
use App\Http\Controllers\Api\V1\ClientController;
use App\Http\Controllers\Api\V1\EnumValeurController;
use App\Http\Controllers\Api\V1\FournisseurController;
use App\Http\Controllers\Api\V1\LivraisonController;
use App\Http\Controllers\Api\V1\PaiementController;
use App\Http\Controllers\Api\V1\ProduitController;
use App\Http\Controllers\Api\V1\StockController;
use App\Http\Controllers\Api\V1\UserController;
use App\Http\Controllers\Api\V1\VenteController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {

    // ── Auth ──────────────────────────────────────────────────────────────
    Route::prefix('auth')->group(function () {
        Route::post('register', [AuthController::class, 'register']);
        Route::post('login',    [AuthController::class, 'login']);

        Route::middleware('auth:sanctum')->group(function () {
            Route::post('logout', [AuthController::class, 'logout']);
            Route::get('me',      [AuthController::class, 'me']);
        });
    });

    Route::middleware('auth:sanctum')->group(function () {

        // ── Référentiels (Enums) — tous les rôles authentifiés ──────────────
        Route::prefix('referentiels/{type}')->group(function () {
            Route::get('/',         [EnumValeurController::class, 'index']);
            Route::post('/',        [EnumValeurController::class, 'store']);
            Route::patch('/{id}',   [EnumValeurController::class, 'update']);
            Route::delete('/{id}',  [EnumValeurController::class, 'destroy']);
        });

        // ── Admin uniquement ─────────────────────────────────────────────────
        Route::middleware('role:admin')->group(function () {
            Route::apiResource('boucheries', BoucherieController::class);
            Route::apiResource('users', UserController::class);
        });

        // ── Admin + Boucher ──────────────────────────────────────────────────
        Route::middleware('role:admin|boucher')->group(function () {
            Route::apiResource('fournisseurs', FournisseurController::class);
            Route::apiResource('clients', ClientController::class);
            Route::apiResource('produits', ProduitController::class);

            Route::get('achats-fournisseurs',             [AchatFournisseurController::class, 'index']);
            Route::post('achats-fournisseurs',            [AchatFournisseurController::class, 'store']);
            Route::get('achats-fournisseurs/{achat}',     [AchatFournisseurController::class, 'show']);

            Route::get('animaux',           [AnimalController::class, 'index']);
            Route::get('animaux/{animal}',  [AnimalController::class, 'show']);

            Route::get('abattages',              [AbattageController::class, 'index']);
            Route::post('abattages',             [AbattageController::class, 'store']);
            Route::get('abattages/{abattage}',   [AbattageController::class, 'show']);

            Route::get('stocks',                        [StockController::class, 'index']);
            Route::get('stocks/{stock}',                [StockController::class, 'show']);
            Route::get('stocks/{stock}/mouvements',     [StockController::class, 'mouvements']);
            Route::post('stocks/{stock}/ajuster',       [StockController::class, 'ajuster']);
        });

        // ── Admin + Boucher + Caissier ───────────────────────────────────────
        Route::middleware('role:admin|boucher|caissier')->group(function () {
            Route::get('ventes',                    [VenteController::class, 'index']);
            Route::post('ventes',                   [VenteController::class, 'store']);
            Route::get('ventes/{vente}',            [VenteController::class, 'show']);
            Route::patch('ventes/{vente}/statut',   [VenteController::class, 'updateStatut']);
            Route::delete('ventes/{vente}',         [VenteController::class, 'destroy']);

            Route::get('ventes/{vente}/paiements',  [PaiementController::class, 'index']);
            Route::post('ventes/{vente}/paiements', [PaiementController::class, 'store']);

            Route::post('ventes/{vente}/livraison',  [LivraisonController::class, 'store']);
            Route::patch('ventes/{vente}/livraison', [LivraisonController::class, 'update']);
        });
    });
});
