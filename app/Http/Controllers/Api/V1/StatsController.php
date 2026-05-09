<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\StatsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @group Statistiques
 * @authenticated
 *
 * Tableau de bord adapté au rôle de l'utilisateur connecté.
 *
 * Le paramètre `periode` filtre les données sur la fenêtre choisie :
 * - `semaine` — 7 derniers jours
 * - `mois` — 30 derniers jours (défaut)
 * - `annee` — 12 derniers mois
 *
 * La réponse varie selon le rôle :
 * - **admin** — vue globale : ventes, stocks, versements, tops boucheries/produits/fournisseurs
 * - **boucher** — pilotage de sa boucherie : ventes, stocks, alertes, distributions, versements, tops produits/clients
 * - **fournisseur** — suivi créances : abattages, distributions, versements (dû vs perçu), tops boucheries clientes
 */
class StatsController extends Controller
{
    public function __construct(private readonly StatsService $service) {}

    /**
     * Tableau de bord
     *
     * Retourne les indicateurs clés adaptés au rôle de l'utilisateur connecté.
     *
     * @queryParam periode string Fenêtre temporelle : `semaine`, `mois` (défaut), `annee`. Example: mois
     *
     * @response scenario="Admin" {
     *   "data": {
     *     "periode": "mois",
     *     "ventes": { "total": 120, "montant_total": 3600000, "montant_moyen": 30000 },
     *     "stocks": { "total_references": 25, "alertes_rupture": 3 },
     *     "versements": {
     *       "en_attente": { "count": 4, "montant": 400000 },
     *       "valides":    { "count": 18, "montant": 1800000 },
     *       "rejetes":    { "count": 2,  "montant": 100000 }
     *     },
     *     "top_produits":    [{ "produit": "Côtelettes", "quantite_vendue": 250, "chiffre_affaires": 750000 }],
     *     "top_boucheries":  [{ "boucherie": "Boucherie Centrale", "nb_ventes": 80, "chiffre_affaires": 2400000 }],
     *     "top_fournisseurs":[{ "fournisseur": "Jean Éleveur", "montant_recu": 900000 }]
     *   }
     * }
     *
     * @response scenario="Boucher" {
     *   "data": {
     *     "periode": "mois",
     *     "ventes": { "total": 45, "montant_total": 1350000, "montant_moyen": 30000 },
     *     "stocks": {
     *       "total_references": 10,
     *       "alertes_rupture": 2,
     *       "produits_en_alerte": [{ "produit": "Gigot", "quantite": 1.5, "seuil_alerte": 5 }]
     *     },
     *     "distributions": { "en_attente": 3, "acceptees": 12, "rejetees": 0 },
     *     "versements": {
     *       "en_attente": { "count": 2, "montant": 200000 },
     *       "valides":    { "count": 7, "montant": 700000 },
     *       "rejetes":    { "count": 1, "montant": 50000 }
     *     },
     *     "top_produits": [{ "produit": "Côtelettes", "quantite_vendue": 90, "chiffre_affaires": 270000 }],
     *     "top_clients":  [{ "client": "Ali Traoré", "nb_ventes": 8, "total_achats": 240000 }]
     *   }
     * }
     *
     * @response scenario="Fournisseur" {
     *   "data": {
     *     "periode": "mois",
     *     "abattages": { "total": 12, "poids_total_kg": 2400, "rendement_moyen": 58.5 },
     *     "distributions": { "en_attente": 4, "acceptees": 7, "rejetees": 1 },
     *     "versements": {
     *       "en_attente": { "count": 3, "montant": 300000 },
     *       "valides":    { "count": 9, "montant": 900000 },
     *       "rejetes":    { "count": 1, "montant": 50000 },
     *       "total_du": 1200000,
     *       "total_percu": 900000
     *     },
     *     "top_boucheries_clientes": [{ "boucherie": "Boucherie Centrale", "nb_distributions": 5, "quantite_totale": 800 }]
     *   }
     * }
     */
    public function index(Request $request): JsonResponse
    {
        $periode = in_array($request->query('periode'), ['semaine', 'mois', 'annee'], true)
            ? $request->query('periode')
            : 'mois';

        $user = $request->user();

        $data = match (true) {
            $user->hasRole('admin')       => $this->service->forAdmin($periode),
            $user->hasRole('boucher')     => $this->service->forBoucher((string) $user->boucherie_id, $periode),
            $user->hasRole('fournisseur') => $this->service->forFournisseur((string) $user->id, $periode),
            default                       => [],
        };

        return response()->json(['data' => $data]);
    }
}
