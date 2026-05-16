<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\RecetteLigne;
use App\Models\StockCategorie;
use App\Repositories\RecetteJournaliereRepository;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use App\Models\RecetteJournaliere;

class RecetteJournaliereService
{
    public function __construct(private readonly RecetteJournaliereRepository $repository) {}

    public function paginateByBoucherie(string $boucherieId): LengthAwarePaginator
    {
        return $this->repository->paginateByBoucherie($boucherieId);
    }

    public function paginateByFournisseur(string $fournisseurId): LengthAwarePaginator
    {
        return $this->repository->paginateByFournisseur($fournisseurId);
    }

    public function paginateAll(): LengthAwarePaginator
    {
        return $this->repository->paginateAll();
    }

    public function findById(string $id): RecetteJournaliere
    {
        return $this->repository->findOrFail($id);
    }

    public function create(array $data, string $boucherieId, string $fournisseurId): RecetteJournaliere
    {
        return DB::transaction(function () use ($data, $boucherieId, $fournisseurId) {
            $lignes = $data['lignes'] ?? [];

            // Calcul du montant_total depuis les lignes
            $montantTotal = collect($lignes)->sum(
                fn ($l) => (float) $l['poids_kg_vendu'] * (float) $l['prix_par_kg']
            );

            $recette = $this->repository->create([
                'boucherie_id'     => $boucherieId,
                'fournisseur_id'   => $fournisseurId,
                'date'             => $data['date'],
                'montant_total'    => $montantTotal,
                'montant_verse'    => $data['montant_verse'] ?? 0,
                'statut_versement' => 'en_attente',
                'notes'            => $data['notes'] ?? null,
            ]);

            foreach ($lignes as $ligne) {
                $montantLigne = (float) $ligne['poids_kg_vendu'] * (float) $ligne['prix_par_kg'];

                RecetteLigne::create([
                    'recette_id'     => $recette->id,
                    'categorie'      => $ligne['categorie'],
                    'poids_kg_vendu' => $ligne['poids_kg_vendu'],
                    'prix_par_kg'    => $ligne['prix_par_kg'],
                    'montant'        => $montantLigne,
                ]);

                // Réduire le stock physique (kg)
                $stock = StockCategorie::firstOrCreate(
                    ['boucherie_id' => $boucherieId, 'categorie' => $ligne['categorie']],
                    ['poids_kg_disponible' => 0]
                );
                $stock->decrement('poids_kg_disponible', (float) $ligne['poids_kg_vendu']);
            }

            return $this->repository->findOrFail($recette->id);
        });
    }

    public function validerVersement(string $id, string $fournisseurId): RecetteJournaliere
    {
        $recette = $this->repository->findOrFail($id);

        if ($recette->fournisseur_id !== $fournisseurId) {
            throw ValidationException::withMessages([
                'recette' => ['Cette recette ne vous appartient pas.'],
            ]);
        }

        if ($recette->statut_versement !== 'en_attente') {
            throw ValidationException::withMessages([
                'statut_versement' => ['Seul un versement en attente peut être validé.'],
            ]);
        }

        return $this->repository->update($id, ['statut_versement' => 'valide']);
    }

    public function rejeterVersement(string $id, string $fournisseurId): RecetteJournaliere
    {
        $recette = $this->repository->findOrFail($id);

        if ($recette->fournisseur_id !== $fournisseurId) {
            throw ValidationException::withMessages([
                'recette' => ['Cette recette ne vous appartient pas.'],
            ]);
        }

        if ($recette->statut_versement !== 'en_attente') {
            throw ValidationException::withMessages([
                'statut_versement' => ['Seul un versement en attente peut être rejeté.'],
            ]);
        }

        return $this->repository->update($id, ['statut_versement' => 'rejete']);
    }
}
