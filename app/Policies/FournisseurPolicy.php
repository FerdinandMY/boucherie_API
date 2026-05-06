<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Fournisseur;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class FournisseurPolicy
{
    use HandlesAuthorization;

    public function before(User $user, string $ability): ?bool
    {
        return $user->hasRole('admin') ? true : null;
    }

    public function view(User $user, Fournisseur $fournisseur): bool
    {
        return $user->boucherie_id === $fournisseur->boucherie_id;
    }

    public function update(User $user, Fournisseur $fournisseur): bool
    {
        return $user->boucherie_id === $fournisseur->boucherie_id;
    }

    public function delete(User $user, Fournisseur $fournisseur): bool
    {
        return $user->boucherie_id === $fournisseur->boucherie_id;
    }
}
