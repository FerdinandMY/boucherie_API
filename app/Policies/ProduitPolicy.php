<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Produit;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProduitPolicy
{
    use HandlesAuthorization;

    public function before(User $user, string $ability): ?bool
    {
        return $user->hasRole('admin') ? true : null;
    }

    public function view(User $user, Produit $produit): bool
    {
        return $user->boucherie_id === $produit->boucherie_id;
    }

    public function update(User $user, Produit $produit): bool
    {
        return $user->boucherie_id === $produit->boucherie_id;
    }

    public function delete(User $user, Produit $produit): bool
    {
        return $user->boucherie_id === $produit->boucherie_id;
    }
}
