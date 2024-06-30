<?php

namespace App\Repositories;

use App\Models\Butcher;


class ButcherShopRepository
{
    public function all()
    {
        return Butcher::paginate(10);
    }

    public function find($id)
    {
        return Butcher::findOrFail($id);
    }

    public function create(array $data)
    {
        return Butcher::create($data);
    }

    public function update($id, array $data)
    {
        $butcherShop = $this->find($id);
        $butcherShop->update($data);
        return $butcherShop;
    }

    public function delete($id)
    {
        $butcherShop = $this->find($id);
        $butcherShop->delete();
        return $butcherShop;
    }
}
