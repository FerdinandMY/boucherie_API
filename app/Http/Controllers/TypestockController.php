<?php

namespace App\Http\Controllers;

use App\Models\Typestock;
use App\Repositories\Interfaces\TypeStockRepositoryInterface;
use Illuminate\Http\Request;

class TypestockController extends Controller
{
    protected $typeStockRepo;

    public function __construct(TypeStockRepositoryInterface $typeStockRepo)
    {
        $this->typeStockRepo = $typeStockRepo;
    }

    public function index(): \Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        $items = $this->typeStockRepo->all();
        return view('type_stocks.index', compact('items'));
    }

    public function create()
    {
        return view('type_stocks.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string'
        ]);

        $this->typeStockRepo->create($data);
        return redirect()->route('type_stocks.index')->with('success', 'Type créé avec succès.');
    }

    public function show($id)
    {
        $item = $this->typeStockRepo->find($id);
        return view('type_stocks.show', compact('item'));
    }

    public function edit($id)
    {
        $item = $this->typeStockRepo->find($id);
        return view('type_stocks.edit', compact('item'));
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string'
        ]);

        $this->typeStockRepo->update($id, $data);
        return redirect()->route('type_stocks.index')->with('success', 'Type mis à jour avec succès.');
    }

    public function destroy($id)
    {
        $this->typeStockRepo->delete($id);
        return redirect()->route('type_stocks.index')->with('success', 'Type supprimé.');
    }
}
