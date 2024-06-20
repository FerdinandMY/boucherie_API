<?php

namespace App\Http\Controllers;

use App\Models\Butcher;
use App\Repositories\ButcherShopRepository;
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
        return ButcherShopResource::collection($butcherShops);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateButcherShopRequest $request)
    {
        $data = $request->validated();
        $butcherShop = $this->repository->create($data);
        return new ButcherShopResource($butcherShop);
    }

    /**
     * Display the specified resource.
     */
    //public function show(Butcher $butcher)
    public function show($id)
    {
        $butcherShop = $this->repository->find($id);
        return new ButcherShopResource($butcherShop);
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
    public function update(UpdateButcherShopRequest $request, $id)
    {
        $data = $request->validated();
        $butcherShop = $this->repository->update($id, $data);
        return new ButcherShopResource($butcherShop);
    }

    /**
     * Remove the specified resource from storage.
     */
    //public function destroy(Butcher $butcher)
    public function destroy($id)
    {
        $this->repository->delete($id);
        return response()->json(null, 204);
    }
}
