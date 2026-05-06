<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Hash;

class UserService
{
    public function paginate(string $boucherieId, int $perPage = 15): LengthAwarePaginator
    {
        return User::query()
            ->where('boucherie_id', $boucherieId)
            ->with('roles')
            ->select(['id', 'name', 'email', 'boucherie_id', 'created_at'])
            ->latest()
            ->paginate($perPage);
    }

    public function findById(string $id): User
    {
        return User::with('roles')->findOrFail($id);
    }

    public function create(array $data): User
    {
        $role = $data['role'] ?? 'boucher';
        unset($data['role']);

        $data['password'] = Hash::make($data['password']);
        $user = User::create($data);
        $user->assignRole($role);

        return $user->load('roles');
    }

    public function update(string $id, array $data): User
    {
        $user = User::findOrFail($id);

        if (isset($data['role'])) {
            $user->syncRoles([$data['role']]);
            unset($data['role']);
        }

        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $user->update($data);

        return $user->load('roles');
    }

    public function delete(string $id): void
    {
        User::findOrFail($id)->delete();
    }
}
