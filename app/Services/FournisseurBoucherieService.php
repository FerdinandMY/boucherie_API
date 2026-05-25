<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Fournisseur;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class FournisseurBoucherieService
{
    /**
     * @param  array<int, string>  $boucherieIds
     */
    public function syncForFournisseur(Fournisseur $fournisseur, array $boucherieIds): void
    {
        $ids = array_values(array_unique(array_filter($boucherieIds)));

        if ($ids === []) {
            $fournisseur->boucheries()->detach();

            return;
        }

        $conflicts = DB::table('fournisseur_boucherie')
            ->whereIn('boucherie_id', $ids)
            ->where('fournisseur_id', '!=', $fournisseur->id)
            ->pluck('boucherie_id')
            ->all();

        if ($conflicts !== []) {
            throw ValidationException::withMessages([
                'boucherie_ids' => [
                    'Une ou plusieurs boucheries sont déjà rattachées à un autre fournisseur.',
                ],
            ]);
        }

        $fournisseur->boucheries()->sync($ids);
    }

    public function assertBoucherieServedByFournisseurUser(string $boucherieId, int $fournisseurUserId): void
    {
        $linked = DB::table('fournisseur_boucherie')
            ->join('fournisseurs', 'fournisseurs.id', '=', 'fournisseur_boucherie.fournisseur_id')
            ->where('fournisseur_boucherie.boucherie_id', $boucherieId)
            ->where('fournisseurs.user_id', $fournisseurUserId)
            ->exists();

        if (! $linked) {
            abort(403, 'Cette boucherie n\'est pas desservie par ce fournisseur.');
        }
    }

    public function fournisseurUserIdForBoucherie(?string $boucherieId): ?int
    {
        if ($boucherieId === null || $boucherieId === '') {
            return null;
        }

        $userId = DB::table('fournisseur_boucherie')
            ->join('fournisseurs', 'fournisseurs.id', '=', 'fournisseur_boucherie.fournisseur_id')
            ->where('fournisseur_boucherie.boucherie_id', $boucherieId)
            ->value('fournisseurs.user_id');

        return $userId !== null ? (int) $userId : null;
    }
}
