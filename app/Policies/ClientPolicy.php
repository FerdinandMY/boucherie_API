<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Client;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ClientPolicy
{
    use HandlesAuthorization;

    public function before(User $user, string $ability): ?bool
    {
        return $user->hasRole('admin') ? true : null;
    }

    public function view(User $user, Client $client): bool
    {
        return $user->boucherie_id === $client->boucherie_id;
    }

    public function update(User $user, Client $client): bool
    {
        return $user->boucherie_id === $client->boucherie_id;
    }

    public function delete(User $user, Client $client): bool
    {
        return $user->boucherie_id === $client->boucherie_id;
    }
}
