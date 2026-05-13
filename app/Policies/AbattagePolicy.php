<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Abattage;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AbattagePolicy
{
    use HandlesAuthorization;

    public function before(User $user, string $ability): ?bool
    {
        return $user->hasRole('admin') ? true : null;
    }

    public function view(User $user, Abattage $abattage): bool
    {
        if ($user->hasRole('fournisseur')) {
            return $abattage->animal?->fournisseur?->user_id === $user->id;
        }

        return $user->boucherie_id !== null
            && $user->boucherie_id === $abattage->boucherie_id;
    }
}
