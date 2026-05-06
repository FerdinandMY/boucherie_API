<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;
use App\Models\Vente;
use Illuminate\Auth\Access\HandlesAuthorization;

class VentePolicy
{
    use HandlesAuthorization;

    public function before(User $user, string $ability): ?bool
    {
        return $user->hasRole('admin') ? true : null;
    }

    public function view(User $user, Vente $vente): bool
    {
        return $user->boucherie_id === $vente->boucherie_id;
    }

    public function update(User $user, Vente $vente): bool
    {
        return $user->boucherie_id === $vente->boucherie_id;
    }

    public function delete(User $user, Vente $vente): bool
    {
        return $user->boucherie_id === $vente->boucherie_id;
    }
}
