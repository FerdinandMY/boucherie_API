<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repositories\Interfaces\TypeStockRepositoryInterface;
use Illuminate\Http\Request;

class TypeStockController extends Controller
{
    protected TypeStockRepositoryInterface $typeStockRepo;

    public function __construct(TypeStockRepositoryInterface $typeStockRepo)
    {
        $this->typeStockRepo = $typeStockRepo;
    }

    public function index(): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'data' => $this->typeStockRepo->all()
        ]);
    }

    public function store(Request $request): \Illuminate\Http\JsonResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);



        $created = $this->typeStockRepo->create($data);

        return response()->json([
            'message' => 'TypeStock créé avec succès',
            'data' => $created
        ], 201);
    }

    public function show($id): \Illuminate\Http\JsonResponse
    {
        try {
            $item = $this->typeStockRepo->find($id);

            return response()->json(['data' => $item]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'TypeStock introuvable'], 404);
        }
    }

    public function update(Request $request, $id): \Illuminate\Http\JsonResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        try {
            $updated = $this->typeStockRepo->update($id, $data);
            return response()->json([
                'message' => 'TypeStock mis à jour',
                'data' => $updated
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'TypeStock non trouvé'], 404);
        }
    }

    public function destroy($id): \Illuminate\Http\JsonResponse
    {
        try {
            $this->typeStockRepo->delete($id);
            return response()->json(['message' => 'TypeStock supprimé']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Suppression échouée'], 404);
        }
    }
}
