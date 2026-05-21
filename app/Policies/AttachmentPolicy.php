<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Abattage;
use App\Models\Attachment;
use App\Models\Boucherie;
use App\Models\Reception;
use App\Models\User;
use App\Models\Vente;
use App\Models\Versement;

class AttachmentPolicy
{
    public function view(User $user, Attachment $attachment): bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }

        if ((int) $attachment->user_id === (int) $user->id) {
            return true;
        }

        $attachable = $attachment->attachable;
        if (! $attachable) {
            return false;
        }

        return match (true) {
            $attachable instanceof Abattage  => app(AbattagePolicy::class)->view($user, $attachable),
            $attachable instanceof Vente     => app(VentePolicy::class)->view($user, $attachable),
            $attachable instanceof Versement => $this->canViewVersement($user, $attachable),
            $attachable instanceof Reception => $this->canViewReception($user, $attachable),
            $attachable instanceof Boucherie => $user->hasRole('admin'),
            default                          => false,
        };
    }

    private function canViewVersement(User $user, Versement $versement): bool
    {
        if ($user->hasRole('fournisseur')) {
            return (int) $versement->fournisseur_user_id === (int) $user->id;
        }

        return $user->boucherie_id !== null
            && $versement->boucherie_id !== null
            && $user->boucherie_id === $versement->boucherie_id;
    }

    private function canViewReception(User $user, Reception $reception): bool
    {
        if ($user->hasRole('fournisseur')) {
            $reception->loadMissing('distribution');

            return (int) $reception->distribution?->fournisseur_user_id === (int) $user->id;
        }

        return $user->boucherie_id !== null
            && $reception->boucherie_id !== null
            && $user->boucherie_id === $reception->boucherie_id;
    }

    public function create(User $user): bool
    {
        return $user !== null;
    }
}
