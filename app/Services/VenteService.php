<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\LigneVente;
use App\Models\MouvementStock;
use App\Models\Stock;
use App\Models\Vente;
use App\Repositories\VenteRepository;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class VenteService
{
    public function __construct(private readonly VenteRepository $repository) {}

    public function paginate(string $boucherieId, array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->paginate($boucherieId, $filters, $perPage);
    }

    public function findById(string $id): Vente
    {
        return $this->repository->findOrFail($id);
    }

    public function create(array $data, int $userId): Vente
    {
        return DB::transaction(function () use ($data, $userId) {
            if ($data['type_vente'] === 'livraison' && empty($data['client_id'])) {
                throw ValidationException::withMessages([
                    'client_id' => ['Un client est obligatoire pour une vente en livraison.'],
                ]);
            }

            $lignes = $data['lignes'] ?? [];
            unset($data['lignes']);

            $data['user_id'] = $userId;
            $data['statut']  = 'en_cours';

            $vente        = $this->repository->create($data);
            $montantTotal = 0;

            foreach ($lignes as $ligneData) {
                $produit   = \App\Models\Produit::findOrFail($ligneData['produit_id']);
                $prix      = $ligneData['prix_unitaire'] ?? (float) $produit->prix_unitaire;
                $quantite  = (float) $ligneData['quantite'];
                $sousTotal = round($prix * $quantite, 2);

                $stock = Stock::where('boucherie_id', $vente->boucherie_id)
                    ->where('produit_id', $ligneData['produit_id'])
                    ->first();

                if (!$stock || (float) $stock->quantite < $quantite) {
                    throw ValidationException::withMessages([
                        'lignes' => ["Stock insuffisant pour le produit {$produit->nom}."],
                    ]);
                }

                $stock->decrement('quantite', $quantite);

                MouvementStock::create([
                    'stock_id' => $stock->id,
                    'user_id'  => $userId,
                    'type'     => 'sortie',
                    'quantite' => $quantite,
                    'motif'    => "Vente #{$vente->id}",
                ]);

                LigneVente::create([
                    'vente_id'      => $vente->id,
                    'produit_id'    => $ligneData['produit_id'],
                    'quantite'      => $quantite,
                    'prix_unitaire' => $prix,
                    'sous_total'    => $sousTotal,
                ]);

                $montantTotal += $sousTotal;
            }

            $vente->update(['montant_total' => $montantTotal]);

            return $vente->fresh(['client', 'lignes.produit', 'paiements']);
        });
    }

    public function updateStatut(string $id, array $data, int $userId): Vente
    {
        return DB::transaction(function () use ($id, $data, $userId) {
            $vente         = $this->repository->findOrFail($id);
            $nouveauStatut = $data['statut'];

            if ($nouveauStatut === 'annulee') {
                if (!in_array($vente->statut, ['en_cours', 'validee'], true)) {
                    throw ValidationException::withMessages([
                        'statut' => ['Cette vente ne peut plus être annulée.'],
                    ]);
                }

                foreach ($vente->lignes as $ligne) {
                    $stock = Stock::where('boucherie_id', $vente->boucherie_id)
                        ->where('produit_id', $ligne->produit_id)
                        ->first();

                    if ($stock) {
                        $stock->increment('quantite', (float) $ligne->quantite);

                        MouvementStock::create([
                            'stock_id' => $stock->id,
                            'user_id'  => $userId,
                            'type'     => 'entree',
                            'quantite' => $ligne->quantite,
                            'motif'    => "Annulation vente #{$vente->id}",
                        ]);
                    }
                }
            }

            $vente->update(['statut' => $nouveauStatut]);

            return $vente->fresh(['client', 'lignes.produit', 'paiements']);
        });
    }

    public function delete(string $id): void
    {
        $this->repository->findOrFail($id)->delete();
    }
}
