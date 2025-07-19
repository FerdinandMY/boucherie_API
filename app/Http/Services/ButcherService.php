<?php

namespace App\Http\Services;

use App\Models\Butcher;
use Illuminate\Database\Eloquent\Collection;

class ButcherService
{
    public function list(): Collection
    {
        return Butcher::all();
    }

    public function create(array $data): Butcher
    {
        return Butcher::create($data);
    }

    public function find(int $id): Butcher
    {
        return Butcher::findOrFail($id);
    }

    public function update(Butcher $butcher, array $data): Butcher
    {
        $butcher->update($data);
        return $butcher;
    }

    public function delete(Butcher $butcher): void
    {
        $butcher->delete();
    }
}

