<?php

declare(strict_types=1);

use App\Models\Boucherie;
use App\Models\Fournisseur;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class)->in('Feature', 'Unit');

beforeEach(function () {
    app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

    Role::firstOrCreate(['name' => 'admin',       'guard_name' => 'web']);
    Role::firstOrCreate(['name' => 'boucher',     'guard_name' => 'web']);
    Role::firstOrCreate(['name' => 'fournisseur', 'guard_name' => 'web']);
});

// ── Helpers ────────────────────────────────────────────────────────────────

function adminUser(?string $boucherieId = null): User
{
    $user = User::factory()->create(['boucherie_id' => $boucherieId]);
    $user->assignRole('admin');
    return $user;
}

function boucherUser(Boucherie $boucherie): User
{
    $user = User::factory()->create(['boucherie_id' => $boucherie->id]);
    $user->assignRole('boucher');
    return $user;
}

function fournisseurUser(?Fournisseur $fournisseur = null): User
{
    $user = User::factory()->create(['boucherie_id' => null]);
    $user->assignRole('fournisseur');

    if ($fournisseur) {
        $fournisseur->update(['user_id' => $user->id]);
    }

    return $user;
}
