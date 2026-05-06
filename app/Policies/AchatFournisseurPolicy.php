<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\AchatFournisseur;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AchatFournisseurPolicy
{
    use HandlesAuthorization;

    public function before(User $user, string $ability): ?bool
    {
        return $user->hasRole('admin') ? true : null;
    }

    public function view(User $user, AchatFournisseur $achat): bool
    {
        return $user->boucherie_id === $achat->boucherie_id;
    }
}
