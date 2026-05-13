<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Versement;
use App\Repositories\VersementRepository;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Validation\ValidationException;

class VersementService
{
    public function __construct(private readonly VersementRepository $repository) {}

    public function paginateByBoucherie(string $boucherieId): LengthAwarePaginator
    {
        return $this->repository->paginateByBoucherie($boucherieId);
    }

    public function paginateByFournisseur(int $fournisseurUserId): LengthAwarePaginator
    {
        return $this->repository->paginateByFournisseur($fournisseurUserId);
    }

    public function paginateAll(): LengthAwarePaginator
    {
        return $this->repository->paginateAll();
    }

    public function findById(string $id): Versement
    {
        return $this->repository->findOrFail($id);
    }

    public function create(array $data, string $boucherieId): Versement
    {
        $data['boucherie_id'] = $boucherieId;
        $data['statut']       = 'en_attente';

        return $this->repository->create($data);
    }

    public function valider(string $id, int $fournisseurUserId): Versement
    {
        $versement = $this->repository->findOrFail($id);

        $this->assertFournisseurOwns($versement, $fournisseurUserId);
        $this->assertStatut($versement, 'en_attente', 'valider');

        return $this->repository->update($id, [
            'statut'    => 'valide',
            'valide_par' => $fournisseurUserId,
            'valide_le'  => now(),
        ]);
    }

    public function rejeter(string $id, int $fournisseurUserId, ?string $motif): Versement
    {
        $versement = $this->repository->findOrFail($id);

        $this->assertFournisseurOwns($versement, $fournisseurUserId);
        $this->assertStatut($versement, 'en_attente', 'rejeter');

        return $this->repository->update($id, [
            'statut'       => 'rejete',
            'motif_rejet'  => $motif,
            'valide_par'   => $fournisseurUserId,
            'valide_le'    => now(),
        ]);
    }

    private function assertFournisseurOwns(Versement $versement, int $fournisseurUserId): void
    {
        if ($versement->fournisseur_user_id !== $fournisseurUserId) {
            throw ValidationException::withMessages([
                'versement' => ['Ce versement ne vous est pas destiné.'],
            ]);
        }
    }

    private function assertStatut(Versement $versement, string $expected, string $action): void
    {
        if ($versement->statut !== $expected) {
            throw ValidationException::withMessages([
                'statut' => ["Impossible de {$action} un versement au statut « {$versement->statut} »."],
            ]);
        }
    }
}
