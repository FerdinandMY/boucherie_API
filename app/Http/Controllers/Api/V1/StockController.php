<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\AjusterStockRequest;
use App\Http\Resources\MouvementStockResource;
use App\Http\Resources\StockResource;
use App\Services\StockService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

/**
 * @group Stocks
 * @authenticated
 * Consultation et ajustement des stocks. Chaque ajustement génère un mouvement de stock tracé.
 */
class StockController extends Controller
{
    public function __construct(private readonly StockService $service) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        return StockResource::collection(
            $this->service->paginate($request->user()->boucherie_id)
        );
    }

    public function show(string $id): JsonResponse
    {
        $stock = $this->service->getById($id);
        $this->authorize('view', $stock);

        return response()->json([
            'data' => new StockResource($stock),
        ]);
    }

    public function mouvements(string $id): AnonymousResourceCollection
    {
        $stock = $this->service->getById($id);
        $this->authorize('view', $stock);

        return MouvementStockResource::collection($this->service->mouvements($id));
    }

    public function ajuster(AjusterStockRequest $request, string $id): JsonResponse
    {
        $stock = $this->service->getById($id);
        $this->authorize('update', $stock);

        $stock = $this->service->ajuster($id, $request->validated(), $request->user()->id);

        return response()->json([
            'data'    => new StockResource($stock),
            'message' => 'Stock ajusté avec succès.',
        ]);
    }
}
