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
        // Un fournisseur ne peut voir que sa propre entité fournisseur
        if ($user->hasRole('fournisseur')) {
            return $user->fournisseur?->id === $fournisseur->id;
        }

        // Un boucher voit uniquement les fournisseurs de sa boucherie (boucherie_id non null requis)
        return $user->boucherie_id !== null
            && $fournisseur->boucherie_id !== null
            && $user->boucherie_id === $fournisseur->boucherie_id;
    }

    public function update(User $user, Fournisseur $fournisseur): bool
    {
        if ($user->hasRole('fournisseur')) {
            return $user->fournisseur?->id === $fournisseur->id;
        }

        return $user->boucherie_id !== null
            && $fournisseur->boucherie_id !== null
            && $user->boucherie_id === $fournisseur->boucherie_id;
    }

    public function delete(User $user, Fournisseur $fournisseur): bool
    {
        if ($user->hasRole('fournisseur')) {
            return false; // Un fournisseur ne peut pas supprimer son entité
        }

        return $user->boucherie_id !== null
            && $fournisseur->boucherie_id !== null
            && $user->boucherie_id === $fournisseur->boucherie_id;
    }
}
