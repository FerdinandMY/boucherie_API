<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateButcherRequest;
use App\Http\Requests\UpdateButcherRequest;
use App\Models\Butcher;
use App\Repositories\ButcherShopRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ButcherController extends Controller
{
    protected $repository;

    public function __construct(ButcherShopRepository $repository)
    {
        $this->repository = $repository;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $butcherShops = $this->repository->all();
        return response()->json($butcherShops, 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateButcherRequest $request): JsonResponse
    {
        $data = $request->validated();
        return response()->json($this->repository->create($data), 201);
    }

    /**
     * Display the specified resource.
     */
    //public function show(Butcher $butcher)
    public function show($id): JsonResponse
    {
        return response()->json($this->repository->find($id));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Butcher $butcher)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateButcherRequest $request, $id): JsonResponse
    {
        $data = $request->validated();
        return response()->json($this->repository->update($id, $data));
    }

    /**
     * Remove the specified resource from storage.
     */
    //public function destroy(Butcher $butcher)
    public function destroy($id): JsonResponse
    {
        $this->repository->delete($id);
        return response()->json(null, 204);
    }
}
