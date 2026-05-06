<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Stock;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class StockPolicy
{
    use HandlesAuthorization;

    public function before(User $user, string $ability): ?bool
    {
        return $user->hasRole('admin') ? true : null;
    }

    public function view(User $user, Stock $stock): bool
    {
        return $user->boucherie_id === $stock->boucherie_id;
    }

    public function update(User $user, Stock $stock): bool
    {
        return $user->boucherie_id === $stock->boucherie_id;
    }
}
