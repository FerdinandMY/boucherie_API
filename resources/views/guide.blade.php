<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guide d'utilisation — Boucherie API</title>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --admin:       #6366f1;
            --admin-light: #eef2ff;
            --boucher:     #dc2626;
            --boucher-light: #fef2f2;
            --fourn:       #16a34a;
            --fourn-light: #f0fdf4;
            --v2:          #0891b2;
            --v2-light:    #ecfeff;
            --v1:          #92400e;
            --v1-light:    #fffbeb;
            --gray:        #64748b;
            --border:      #e2e8f0;
            --bg:          #f8fafc;
        }

        body { font-family: 'Segoe UI', system-ui, sans-serif; background: var(--bg); color: #1e293b; line-height: 1.6; }

        /* ── HEADER ── */
        header {
            background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
            color: #fff;
            padding: 3rem 2rem 2.5rem;
            text-align: center;
        }
        header h1 { font-size: 2rem; font-weight: 700; letter-spacing: -0.5px; }
        header p  { margin-top: .5rem; color: #94a3b8; font-size: 1.05rem; }

        /* ── NAV PILLS ── */
        .pills {
            display: flex;
            justify-content: center;
            gap: .75rem;
            flex-wrap: wrap;
            padding: 1.5rem 1rem;
            background: #fff;
            border-bottom: 1px solid var(--border);
            position: sticky; top: 0; z-index: 10;
        }
        .pill {
            padding: .45rem 1.25rem;
            border-radius: 9999px;
            font-size: .9rem;
            font-weight: 600;
            text-decoration: none;
            border: 2px solid transparent;
            transition: .15s;
        }
        .pill-admin   { background: var(--admin-light);   color: var(--admin);   border-color: var(--admin); }
        .pill-boucher { background: var(--boucher-light); color: var(--boucher); border-color: var(--boucher); }
        .pill-fourn   { background: var(--fourn-light);   color: var(--fourn);   border-color: var(--fourn); }
        .pill-global  { background: #f1f5f9; color: #334155; border-color: #cbd5e1; }
        .pill:hover   { filter: brightness(.93); }

        /* ── LAYOUT ── */
        main { max-width: 960px; margin: 0 auto; padding: 2.5rem 1.25rem 4rem; }

        /* ── SECTION TITLE ── */
        .section { margin-bottom: 3.5rem; }
        .section-header {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 3px solid var(--border);
        }
        .badge {
            display: inline-flex;
            align-items: center;
            gap: .4rem;
            padding: .4rem 1rem;
            border-radius: 9999px;
            font-weight: 700;
            font-size: .85rem;
            text-transform: uppercase;
            letter-spacing: .5px;
        }
        .badge-admin   { background: var(--admin);   color: #fff; }
        .badge-boucher { background: var(--boucher); color: #fff; }
        .badge-fourn   { background: var(--fourn);   color: #fff; }
        .badge-v2      { background: var(--v2);      color: #fff; }
        .section-title { font-size: 1.4rem; font-weight: 700; }

        /* ── VERSION TAGS ── */
        .vtag {
            display: inline-flex; align-items: center;
            padding: .1rem .45rem;
            border-radius: 4px;
            font-size: .72rem;
            font-weight: 700;
            letter-spacing: .4px;
            text-transform: uppercase;
            vertical-align: middle;
            margin-left: .35rem;
        }
        .vtag-v1 { background: var(--v1-light); border: 1px solid var(--v1); color: var(--v1); }
        .vtag-v2 { background: var(--v2-light); border: 1px solid var(--v2); color: var(--v2); }

        /* ── V2 HIGHLIGHT BOX ── */
        .v2-box {
            background: var(--v2-light);
            border: 1px solid var(--v2);
            border-left: 4px solid var(--v2);
            border-radius: 8px;
            padding: .75rem 1rem;
            margin-top: .75rem;
            font-size: .88rem;
        }
        .v2-box strong { color: var(--v2); }

        /* ── FLOW ── */
        .flow { display: flex; flex-direction: column; gap: .5rem; }

        .step {
            display: grid;
            grid-template-columns: 44px 1fr;
            gap: 1rem;
            align-items: start;
        }

        .step-num {
            width: 44px; height: 44px;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-weight: 700; font-size: 1rem;
            flex-shrink: 0;
            margin-top: .15rem;
        }
        .num-admin   { background: var(--admin);   color: #fff; }
        .num-boucher { background: var(--boucher); color: #fff; }
        .num-fourn   { background: var(--fourn);   color: #fff; }
        .num-gray    { background: #e2e8f0; color: #475569; }

        .step-body {
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 1rem 1.25rem;
            box-shadow: 0 1px 3px rgba(0,0,0,.05);
        }
        .step-body h3 { font-size: 1rem; font-weight: 700; margin-bottom: .3rem; }
        .step-body p  { font-size: .92rem; color: #475569; }

        .step-body .endpoint {
            display: inline-block;
            margin-top: .5rem;
            background: #f1f5f9;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            padding: .2rem .6rem;
            font-family: monospace;
            font-size: .82rem;
            color: #334155;
        }
        .method { font-weight: 700; margin-right: .3rem; }
        .method.post   { color: #16a34a; }
        .method.get    { color: #2563eb; }
        .method.patch  { color: #d97706; }
        .method.delete { color: #dc2626; }

        /* ── ARROW ── */
        .arrow {
            display: flex;
            align-items: center;
            padding-left: 22px; /* center under step-num */
        }
        .arrow::before {
            content: '';
            display: block;
            width: 2px;
            height: 28px;
            background: var(--border);
            margin-left: 21px;
        }

        /* ── GLOBAL FLOW ── */
        .global-flow {
            display: flex;
            align-items: stretch;
            gap: 0;
            flex-wrap: wrap;
            margin-top: .5rem;
        }
        .gf-actor {
            flex: 1;
            min-width: 180px;
            border-radius: 12px;
            overflow: hidden;
            border: 1px solid var(--border);
        }
        .gf-header {
            padding: .6rem 1rem;
            font-weight: 700;
            font-size: .9rem;
            color: #fff;
            text-align: center;
        }
        .gf-header.admin   { background: var(--admin); }
        .gf-header.boucher { background: var(--boucher); }
        .gf-header.fourn   { background: var(--fourn); }
        .gf-body { background: #fff; padding: .75rem 1rem; }
        .gf-body ol { padding-left: 1.2rem; font-size: .88rem; color: #475569; }
        .gf-body li { margin-bottom: .3rem; }

        .gf-arrow {
            display: flex;
            align-items: center;
            padding: 0 .5rem;
            font-size: 1.4rem;
            color: #94a3b8;
        }

        /* ── INFO BOX ── */
        .info-box {
            background: #fff;
            border: 1px solid var(--border);
            border-left: 4px solid #6366f1;
            border-radius: 8px;
            padding: 1rem 1.25rem;
            margin-top: 1rem;
            font-size: .9rem;
            color: #475569;
        }
        .info-box strong { color: #1e293b; }

        /* ── CREDENTIALS ── */
        .cred-table { width: 100%; border-collapse: collapse; margin-top: .75rem; }
        .cred-table th, .cred-table td {
            padding: .6rem 1rem;
            text-align: left;
            border: 1px solid var(--border);
            font-size: .9rem;
        }
        .cred-table th { background: #f1f5f9; font-weight: 600; }
        .cred-table td code { font-family: monospace; background: #f1f5f9; padding: .1rem .4rem; border-radius: 4px; }

        @media (max-width: 640px) {
            header h1 { font-size: 1.4rem; }
            .global-flow { flex-direction: column; }
            .gf-arrow { transform: rotate(90deg); align-self: center; padding: .25rem 0; }
        }
    </style>
</head>
<body>

<header>
    <h1>🥩 Guide d'utilisation — Boucherie API</h1>
    <p>Flux d'utilisation du système par rôle</p>
    <p style="margin-top:.6rem;font-size:.88rem;color:#64748b;">
        <span style="background:#0891b2;color:#fff;padding:.15rem .5rem;border-radius:4px;font-weight:700;font-size:.78rem;">v2</span>
        &nbsp;Nouveau flux catégorie (abattage_lignes · distribution_lignes · recettes_journalieres)
        &nbsp;&nbsp;
        <span style="background:#92400e;color:#fff;padding:.15rem .5rem;border-radius:4px;font-weight:700;font-size:.78rem;">v1</span>
        &nbsp;Flux produits/ventes maintenu en parallèle
    </p>
</header>

<nav class="pills">
    <a class="pill pill-global"  href="#global">Vue globale</a>
    <a class="pill pill-admin"   href="#admin">Administrateur</a>
    <a class="pill pill-boucher" href="#boucher">Boucher</a>
    <a class="pill pill-fourn"   href="#fournisseur">Fournisseur</a>
    <a class="pill pill-global"  href="#stats">Statistiques</a>
    <a class="pill" style="background:var(--v2-light);color:var(--v2);border:2px solid var(--v2);" href="#v2">Nouveautés v2</a>
    <a class="pill pill-global"  href="#comptes">Comptes de test</a>
</nav>

<main>

    <!-- ══════════════════════════════════════════
         VUE GLOBALE
    ══════════════════════════════════════════ -->
    <section class="section" id="global">
        <div class="section-header">
            <span class="badge" style="background:#334155;color:#fff;">🔄 Global</span>
            <h2 class="section-title">Vue d'ensemble du flux</h2>
        </div>

        <div class="global-flow">
            <div class="gf-actor">
                <div class="gf-header fourn">Fournisseur</div>
                <div class="gf-body">
                    <ol>
                        <li>Enregistre ses animaux</li>
                        <li>Effectue un abattage</li>
                        <li>Distribue les lots aux boucheries</li>
                        <li>Reçoit les versements</li>
                        <li>Valide ou rejette les versements</li>
                    </ol>
                </div>
            </div>

            <div class="gf-arrow">→</div>

            <div class="gf-actor">
                <div class="gf-header boucher">Boucher</div>
                <div class="gf-body">
                    <ol>
                        <li>Réceptionne les lots</li>
                        <li><span class="vtag vtag-v1">v1</span> Crée les ventes produit par produit</li>
                        <li><span class="vtag vtag-v2">v2</span> Enregistre la recette du jour (ventes + versement en un seul acte)</li>
                        <li>Suit le stock (unités v1 · kg par catégorie v2)</li>
                    </ol>
                </div>
            </div>

            <div class="gf-arrow">→</div>

            <div class="gf-actor">
                <div class="gf-header admin">Administrateur</div>
                <div class="gf-body">
                    <ol>
                        <li>Configure les boucheries</li>
                        <li>Gère les comptes utilisateurs</li>
                        <li>Supervise l'ensemble du système</li>
                        <li>Accède à toutes les données</li>
                    </ol>
                </div>
            </div>
        </div>

        <div class="info-box">
            <strong>Authentification :</strong> toutes les requêtes (sauf login/register) nécessitent le header
            <code>Authorization: Bearer &lt;token&gt;</code>. Le token est retourné à la connexion.
        </div>
    </section>


    <!-- ══════════════════════════════════════════
         ADMINISTRATEUR
    ══════════════════════════════════════════ -->
    <section class="section" id="admin">
        <div class="section-header">
            <span class="badge badge-admin">👑 Admin</span>
            <h2 class="section-title">Processus Administrateur</h2>
        </div>

        <div class="flow">

            <div class="step">
                <div class="step-num num-admin">1</div>
                <div class="step-body">
                    <h3>Connexion</h3>
                    <p>L'administrateur se connecte avec ses identifiants et récupère son token Bearer.</p>
                    <span class="endpoint"><span class="method post">POST</span>/api/v1/auth/login</span>
                </div>
            </div>
            <div class="arrow"></div>

            <div class="step">
                <div class="step-num num-admin">2</div>
                <div class="step-body">
                    <h3>Créer une boucherie</h3>
                    <p>Enregistre un nouveau point de vente avec ses informations (nom, adresse, ville, téléphone).</p>
                    <span class="endpoint"><span class="method post">POST</span>/api/v1/boucheries</span>
                </div>
            </div>
            <div class="arrow"></div>

            <div class="step">
                <div class="step-num num-admin">3</div>
                <div class="step-body">
                    <h3>Créer les comptes utilisateurs</h3>
                    <p>L'admin crée les comptes pour tous les rôles. Le corps de la requête diffère selon le rôle :</p>

                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:.75rem;margin-top:.75rem;">
                        <div style="background:#eef2ff;border:1px solid #c7d2fe;border-radius:8px;padding:.75rem;">
                            <div style="font-weight:700;font-size:.82rem;color:#4338ca;margin-bottom:.4rem;">BOUCHER / ADMIN</div>
                            <pre style="font-size:.75rem;color:#334155;white-space:pre-wrap;margin:0;">{
  "name": "Alice Martin",
  "email": "alice@test.com",
  "password": "motdepasse8",
  "role": "boucher",
  "boucherie_id": "&lt;uuid&gt;"
}</pre>
                        </div>
                        <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:8px;padding:.75rem;">
                            <div style="font-weight:700;font-size:.82rem;color:#15803d;margin-bottom:.4rem;">FOURNISSEUR</div>
                            <pre style="font-size:.75rem;color:#334155;white-space:pre-wrap;margin:0;">{
  "name": "Jean Éleveur",
  "email": "jean@elevage.com",
  "password": "motdepasse8",
  "role": "fournisseur",
  "fournisseur": {
    "nom": "Élevage du Sahel",
    "contact": "Jean Éleveur",
    "telephone": "+22600000001"
  }
}</pre>
                        </div>
                    </div>
                    <p style="margin-top:.6rem;font-size:.82rem;color:#64748b;">
                        Un boucher ou admin doit obligatoirement avoir un <code>boucherie_id</code>.<br>
                        Pour un fournisseur, ni <code>boucherie_id</code> ni l'objet <code>fournisseur</code> ne sont obligatoires à la création — l'admin peut compléter l'entité fournisseur plus tard via <code>PATCH /api/v1/users/{id}</code>.
                    </p>
                    <span class="endpoint" style="margin-top:.5rem;"><span class="method post">POST</span>/api/v1/users</span>
                    &nbsp;
                    <span class="endpoint"><span class="method get">GET</span>/api/v1/users</span>
                </div>
            </div>
            <div class="arrow"></div>

            <div class="step">
                <div class="step-num num-admin">4</div>
                <div class="step-body">
                    <h3>Configurer les référentiels (Enums)</h3>
                    <p>Paramètre les listes de valeurs : espèces animales, unités de mesure, modes de paiement, catégories de produits, statuts...</p>
                    <span class="endpoint"><span class="method get">GET</span>/api/v1/referentiels/{type}</span>
                    &nbsp;
                    <span class="endpoint"><span class="method post">POST</span>/api/v1/referentiels/{type}</span>
                </div>
            </div>
            <div class="arrow"></div>

            <div class="step">
                <div class="step-num num-admin">5</div>
                <div class="step-body">
                    <h3>Supervision globale</h3>
                    <p>L'admin a accès en lecture et écriture à toutes les ressources : ventes, stocks, abattages, distributions, versements, etc.</p>
                    <span class="endpoint"><span class="method get">GET</span>/api/v1/stocks</span>
                    &nbsp;
                    <span class="endpoint"><span class="method get">GET</span>/api/v1/versements</span>
                    &nbsp;
                    <span class="endpoint"><span class="method get">GET</span>/api/v1/ventes</span>
                </div>
            </div>
            <div class="arrow"></div>

            <div class="step">
                <div class="step-num num-admin">6</div>
                <div class="step-body">
                    <h3>Modifier ou désactiver un compte</h3>
                    <p>Met à jour les informations d'un utilisateur ou le supprime si nécessaire.</p>
                    <span class="endpoint"><span class="method patch">PATCH</span>/api/v1/users/{id}</span>
                    &nbsp;
                    <span class="endpoint"><span class="method delete">DELETE</span>/api/v1/users/{id}</span>
                </div>
            </div>

        </div>
    </section>


    <!-- ══════════════════════════════════════════
         BOUCHER
    ══════════════════════════════════════════ -->
    <section class="section" id="boucher">
        <div class="section-header">
            <span class="badge badge-boucher">🔪 Boucher</span>
            <h2 class="section-title">Processus Boucher</h2>
        </div>

        <div class="flow">

            <div class="step">
                <div class="step-num num-boucher">1</div>
                <div class="step-body">
                    <h3>Connexion</h3>
                    <p>Le boucher se connecte et récupère son token Bearer.</p>
                    <span class="endpoint"><span class="method post">POST</span>/api/v1/auth/login</span>
                </div>
            </div>
            <div class="arrow"></div>

            <div class="step">
                <div class="step-num num-boucher">2</div>
                <div class="step-body">
                    <h3>Consulter les distributions reçues</h3>
                    <p>Le boucher voit les lots de viande que le fournisseur lui a envoyés et qui attendent réception.</p>
                    <span class="endpoint"><span class="method get">GET</span>/api/v1/distributions</span>
                </div>
            </div>
            <div class="arrow"></div>

            <div class="step">
                <div class="step-num num-boucher">3</div>
                <div class="step-body">
                    <h3>Réceptionner un lot</h3>
                    <p>Le boucher confirme la réception d'un lot distribué par le fournisseur. <strong>Le stock est automatiquement alimenté.</strong></p>
                    <span class="endpoint"><span class="method post">POST</span>/api/v1/receptions</span>

                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:.75rem;margin-top:.75rem;">
                        <div style="background:var(--v1-light);border:1px solid #fcd34d;border-radius:8px;padding:.75rem;">
                            <div style="font-weight:700;font-size:.78rem;color:var(--v1);margin-bottom:.4rem;">v1 — quantité globale</div>
                            <pre style="font-size:.75rem;color:#334155;white-space:pre-wrap;margin:0;">{
  "distribution_id": "&lt;uuid&gt;",
  "quantite_recue": 50,
  "date_reception": "2026-05-16"
}</pre>
                        </div>
                        <div style="background:var(--v2-light);border:1px solid var(--v2);border-radius:8px;padding:.75rem;">
                            <div style="font-weight:700;font-size:.78rem;color:var(--v2);margin-bottom:.4rem;">v2 — lignes par catégorie</div>
                            <pre style="font-size:.75rem;color:#334155;white-space:pre-wrap;margin:0;">{
  "distribution_id": "&lt;uuid&gt;",
  "quantite_recue": 115,
  "date_reception": "2026-05-16",
  "lignes": [
    { "categorie": "viande_rouge",
      "poids_kg_attendu": 100,
      "poids_kg_recu": 98 },
    { "categorie": "abats",
      "poids_kg_attendu": 15,
      "poids_kg_recu": 15 }
  ]
}</pre>
                            <p style="margin-top:.4rem;font-size:.78rem;color:#0e7490;">→ Met à jour <code>stocks_categories</code> automatiquement.</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="arrow"></div>

            <div class="step">
                <div class="step-num num-boucher">4</div>
                <div class="step-body">
                    <h3>Gérer le catalogue de produits</h3>
                    <p>Crée et met à jour les produits vendus (côtelettes, filet, gigot…) avec leur unité et prix unitaire.</p>
                    <span class="endpoint"><span class="method post">POST</span>/api/v1/produits</span>
                    &nbsp;
                    <span class="endpoint"><span class="method get">GET</span>/api/v1/produits</span>
                </div>
            </div>
            <div class="arrow"></div>

            <div class="step">
                <div class="step-num num-boucher">5</div>
                <div class="step-body">
                    <h3>Gérer les clients</h3>
                    <p>Enregistre les clients de la boucherie (nom, téléphone, email).</p>
                    <span class="endpoint"><span class="method post">POST</span>/api/v1/clients</span>
                    &nbsp;
                    <span class="endpoint"><span class="method get">GET</span>/api/v1/clients</span>
                </div>
            </div>
            <div class="arrow"></div>

            <div class="step">
                <div class="step-num num-boucher">6</div>
                <div class="step-body">
                    <h3>Enregistrer les ventes <span class="vtag vtag-v1">v1</span></h3>
                    <p>Enregistre une vente produit par produit avec les lignes détaillées. <strong>Le stock (unités) est automatiquement décrémenté.</strong></p>
                    <span class="endpoint"><span class="method post">POST</span>/api/v1/ventes</span>
                    <br><span style="font-size:.82rem;color:#64748b;margin-top:.3rem;display:block">
                        Corps : { client_id, type_vente, lignes: [{ produit_id, quantite, prix_unitaire }] }
                    </span>
                </div>
            </div>
            <div class="arrow"></div>

            <div class="step">
                <div class="step-num num-boucher" style="background:var(--v2);color:#fff;">6</div>
                <div class="step-body" style="border-color:var(--v2);">
                    <h3>Enregistrer la recette journalière + versement <span class="vtag vtag-v2">v2</span></h3>
                    <p>En <strong>un seul formulaire</strong>, le boucher déclare la somme des ventes du jour par catégorie et le montant versé au fournisseur. <strong>Le stock (kg) est automatiquement décrémenté.</strong></p>
                    <span class="endpoint"><span class="method post">POST</span>/api/v1/recettes</span>
                    <div class="v2-box" style="margin-top:.6rem;">
<pre style="font-size:.75rem;color:#334155;white-space:pre-wrap;margin:0;">{
  "date": "2026-05-16",
  "montant_verse": 100000,
  "fournisseur_id": "&lt;uuid&gt;",
  "notes": "Bonne journée",
  "lignes": [
    { "categorie": "viande_rouge", "poids_kg_vendu": 50, "prix_par_kg": 2500 },
    { "categorie": "abats",        "poids_kg_vendu": 10, "prix_par_kg": 1000 }
  ]
}</pre>
                        <p style="margin-top:.5rem;font-size:.82rem;color:#0e7490;">
                            → <code>montant_total</code> calculé automatiquement (Σ poids × prix).<br>
                            → Stock physique (kg) décrémenté dans <code>stocks_categories</code>.<br>
                            → Versement en statut <strong>en_attente</strong> jusqu'à validation du fournisseur.
                        </p>
                    </div>
                </div>
            </div>
            <div class="arrow"></div>

            <div class="step">
                <div class="step-num num-boucher">7</div>
                <div class="step-body">
                    <h3>Enregistrer le paiement client <span class="vtag vtag-v1">v1</span></h3>
                    <p>Enregistre le règlement du client pour une vente donnée (espèces, mobile money…).</p>
                    <span class="endpoint"><span class="method post">POST</span>/api/v1/ventes/{id}/paiements</span>
                </div>
            </div>
            <div class="arrow"></div>

            <div class="step">
                <div class="step-num num-boucher">8</div>
                <div class="step-body">
                    <h3>Effectuer un versement au fournisseur <span class="vtag vtag-v1">v1</span></h3>
                    <p>Déclare un paiement effectué au fournisseur. Le versement passe en statut <strong>en_attente</strong> jusqu'à validation du fournisseur.</p>
                    <span class="endpoint"><span class="method post">POST</span>/api/v1/versements</span>
                    <br><span style="font-size:.82rem;color:#64748b;margin-top:.3rem;display:block">
                        Corps : { fournisseur_user_id, montant, mode_paiement, date_versement, reference? }
                    </span>
                </div>
            </div>
            <div class="arrow"></div>

            <div class="step">
                <div class="step-num num-boucher">9</div>
                <div class="step-body">
                    <h3>Suivre les stocks</h3>
                    <p>Consulte le stock en temps réel, les alertes de rupture et l'historique des mouvements. Peut ajuster manuellement si nécessaire.</p>
                    <span class="endpoint"><span class="method get">GET</span>/api/v1/stocks</span>
                    &nbsp;
                    <span class="endpoint"><span class="method get">GET</span>/api/v1/stocks/{id}/mouvements</span>
                    &nbsp;
                    <span class="endpoint"><span class="method post">POST</span>/api/v1/stocks/{id}/ajuster</span>
                </div>
            </div>

        </div>
    </section>


    <!-- ══════════════════════════════════════════
         FOURNISSEUR
    ══════════════════════════════════════════ -->
    <section class="section" id="fournisseur">
        <div class="section-header">
            <span class="badge badge-fourn">🐄 Fournisseur</span>
            <h2 class="section-title">Processus Fournisseur</h2>
        </div>

        <div class="flow">

            <div class="step">
                <div class="step-num num-fourn">1</div>
                <div class="step-body">
                    <h3>Connexion</h3>
                    <p>Le fournisseur se connecte et récupère son token Bearer.</p>
                    <span class="endpoint"><span class="method post">POST</span>/api/v1/auth/login</span>
                </div>
            </div>
            <div class="arrow"></div>

            <div class="step">
                <div class="step-num num-fourn">2</div>
                <div class="step-body">
                    <h3>Enregistrer un achat d'animaux</h3>
                    <p>Le fournisseur enregistre un lot d'animaux vivants achetés (espèce, poids vif, prix d'achat, numéro de tag). Ces animaux lui appartiennent jusqu'à l'abattage.</p>
                    <span class="endpoint"><span class="method post">POST</span>/api/v1/achats-fournisseurs</span>
                </div>
            </div>
            <div class="arrow"></div>

            <div class="step">
                <div class="step-num num-fourn">3</div>
                <div class="step-body">
                    <h3>Effectuer un abattage</h3>
                    <p>Enregistre l'abattage d'un animal. Le poids de carcasse et le rendement sont calculés. <strong>Aucun stock n'est créé à cette étape</strong> — le stock sera alimenté à la réception par la boucherie.</p>
                    <span class="endpoint"><span class="method post">POST</span>/api/v1/abattages</span>

                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:.75rem;margin-top:.75rem;">
                        <div style="background:var(--v1-light);border:1px solid #fcd34d;border-radius:8px;padding:.75rem;">
                            <div style="font-weight:700;font-size:.78rem;color:var(--v1);margin-bottom:.4rem;">v1 — sans lignes</div>
                            <pre style="font-size:.75rem;color:#334155;white-space:pre-wrap;margin:0;">{
  "animal_id": "&lt;uuid&gt;",
  "date_abattage": "2026-05-16",
  "poids_carcasse_kg": 180,
  "stocks": [
    { "produit_id": "&lt;uuid&gt;", "quantite": 50 }
  ]
}</pre>
                        </div>
                        <div style="background:var(--v2-light);border:1px solid var(--v2);border-radius:8px;padding:.75rem;">
                            <div style="font-weight:700;font-size:.78rem;color:var(--v2);margin-bottom:.4rem;">v2 — lignes par catégorie</div>
                            <pre style="font-size:.75rem;color:#334155;white-space:pre-wrap;margin:0;">{
  "animal_id": "&lt;uuid&gt;",
  "date_abattage": "2026-05-16",
  "poids_carcasse_kg": 180,
  "lignes": [
    { "categorie": "viande_rouge", "poids_kg": 150 },
    { "categorie": "abats",        "poids_kg": 20 },
    { "categorie": "autre",        "poids_kg": 10 }
  ]
}</pre>
                        </div>
                    </div>
                </div>
            </div>
            <div class="arrow"></div>

            <div class="step">
                <div class="step-num num-fourn">4</div>
                <div class="step-body">
                    <h3>Distribuer les lots aux boucheries</h3>
                    <p>Distribution individuelle : <strong>une distribution par boucherie</strong>. Le fournisseur alloue une portion de l'abattage à chaque boucherie.</p>
                    <span class="endpoint"><span class="method post">POST</span>/api/v1/distributions</span>

                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:.75rem;margin-top:.75rem;">
                        <div style="background:var(--v1-light);border:1px solid #fcd34d;border-radius:8px;padding:.75rem;">
                            <div style="font-weight:700;font-size:.78rem;color:var(--v1);margin-bottom:.4rem;">v1 — par produit</div>
                            <pre style="font-size:.75rem;color:#334155;white-space:pre-wrap;margin:0;">{
  "abattage_id": "&lt;uuid&gt;",
  "boucherie_id": "&lt;uuid&gt;",
  "produit_id": "&lt;uuid&gt;",
  "quantite": 50,
  "notes": "..."
}</pre>
                        </div>
                        <div style="background:var(--v2-light);border:1px solid var(--v2);border-radius:8px;padding:.75rem;">
                            <div style="font-weight:700;font-size:.78rem;color:var(--v2);margin-bottom:.4rem;">v2 — lignes par catégorie</div>
                            <pre style="font-size:.75rem;color:#334155;white-space:pre-wrap;margin:0;">{
  "abattage_id": "&lt;uuid&gt;",
  "boucherie_id": "&lt;uuid&gt;",
  "lignes": [
    { "categorie": "viande_rouge",
      "poids_kg": 100,
      "prix_par_kg": 2500 },
    { "categorie": "abats",
      "poids_kg": 15,
      "prix_par_kg": 1000 }
  ]
}</pre>
                        </div>
                    </div>
                </div>
            </div>
            <div class="arrow"></div>

            <div class="step">
                <div class="step-num num-fourn">5</div>
                <div class="step-body">
                    <h3>Suivre l'état des distributions</h3>
                    <p>Le fournisseur consulte le statut de chaque lot : <strong>en_attente</strong> (non reçu), <strong>acceptee</strong> (réceptionné par la boucherie), <strong>rejetee</strong> (annulé).</p>
                    <span class="endpoint"><span class="method get">GET</span>/api/v1/distributions</span>
                </div>
            </div>
            <div class="arrow"></div>

            <div class="step">
                <div class="step-num num-fourn">6</div>
                <div class="step-body">
                    <h3>Consulter les versements reçus</h3>
                    <p>Le fournisseur voit tous les versements déclarés par les boucheries en sa faveur avec leur statut (en_attente, valide, rejete).</p>
                    <span class="endpoint"><span class="method get">GET</span>/api/v1/versements</span>
                </div>
            </div>
            <div class="arrow"></div>

            <div class="step">
                <div class="step-num num-fourn">7</div>
                <div class="step-body">
                    <h3>Valider un versement</h3>
                    <p>Le fournisseur confirme avoir reçu le paiement. Le versement passe en statut <strong>valide</strong>.</p>
                    <span class="endpoint"><span class="method patch">PATCH</span>/api/v1/versements/{id}/valider</span>
                </div>
            </div>
            <div class="arrow"></div>

            <div class="step">
                <div class="step-num num-fourn">8</div>
                <div class="step-body">
                    <h3>Rejeter un versement</h3>
                    <p>Si le montant ou la référence est incorrect, le fournisseur rejette le versement en indiquant un motif. La boucherie devra en créer un nouveau.</p>
                    <span class="endpoint"><span class="method patch">PATCH</span>/api/v1/versements/{id}/rejeter</span>
                    <br><span style="font-size:.82rem;color:#64748b;margin-top:.3rem;display:block">
                        Corps : { motif_rejet: "Montant incorrect, attendu 200 000 FCFA" }
                    </span>
                </div>
            </div>
            <div class="arrow"></div>

            <div class="step">
                <div class="step-num num-fourn">9</div>
                <div class="step-body">
                    <h3>Consulter les paiements par vente</h3>
                    <p>Le fournisseur peut consulter les encaissements clients d'une vente spécifique pour avoir une visibilité sur la trésorerie des boucheries.</p>
                    <span class="endpoint"><span class="method get">GET</span>/api/v1/ventes/{id}/paiements</span>
                </div>
            </div>

        </div>
    </section>


    <!-- ══════════════════════════════════════════
         STATISTIQUES
    ══════════════════════════════════════════ -->
    <section class="section" id="stats">
        <div class="section-header">
            <span class="badge" style="background:#0ea5e9;color:#fff;">📊 Stats</span>
            <h2 class="section-title">Tableau de bord — Statistiques</h2>
        </div>

        <div class="info-box" style="border-left-color:#0ea5e9;margin-bottom:1.25rem;">
            Chaque rôle dispose de son propre endpoint dédié. Le paramètre <code>?periode=</code> est optionnel (défaut : <strong>mois</strong>).<br><br>
            <span class="endpoint"><span class="method get">GET</span>/api/v1/stats/admin</span>
            &nbsp;— Admin uniquement<br>
            <span class="endpoint"><span class="method get">GET</span>/api/v1/stats/boucher</span>
            &nbsp;— Boucher (+ admin)<br>
            <span class="endpoint"><span class="method get">GET</span>/api/v1/stats/fournisseur</span>
            &nbsp;— Fournisseur (+ admin)
        </div>

        <!-- ADMIN -->
        <div style="margin-bottom:2rem;">
            <div style="display:flex;align-items:center;gap:.75rem;margin-bottom:1rem;">
                <span class="badge badge-admin">👑 Admin</span>
                <span style="font-weight:600;font-size:1rem;">Vue globale</span>
            </div>
            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:.75rem;">
                <div class="step-body">
                    <h3>Ventes</h3>
                    <p>Nombre total, montant total et montant moyen des ventes sur la période, toutes boucheries confondues.</p>
                </div>
                <div class="step-body">
                    <h3>Stocks</h3>
                    <p>Nombre total de références en stock et nombre de produits en alerte de rupture.</p>
                </div>
                <div class="step-body">
                    <h3>Versements</h3>
                    <p>Répartition des versements par statut (en_attente, valides, rejetés) avec montants.</p>
                </div>
                <div class="step-body">
                    <h3>Top produits</h3>
                    <p>5 produits les plus vendus (quantité + chiffre d'affaires) sur la période.</p>
                </div>
                <div class="step-body">
                    <h3>Top boucheries</h3>
                    <p>5 boucheries avec le plus grand chiffre d'affaires sur la période.</p>
                </div>
                <div class="step-body">
                    <h3>Top fournisseurs</h3>
                    <p>5 fournisseurs ayant reçu le plus de versements validés.</p>
                </div>
            </div>
        </div>

        <!-- BOUCHER -->
        <div style="margin-bottom:2rem;">
            <div style="display:flex;align-items:center;gap:.75rem;margin-bottom:1rem;">
                <span class="badge badge-boucher">🔪 Boucher</span>
                <span style="font-weight:600;font-size:1rem;">Pilotage de la boucherie</span>
            </div>
            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:.75rem;">
                <div class="step-body">
                    <h3>Ventes</h3>
                    <p>Nombre, montant total et montant moyen des ventes de sa boucherie sur la période.</p>
                </div>
                <div class="step-body">
                    <h3>Stocks & alertes</h3>
                    <p>Nombre de références, nombre d'alertes de rupture et liste des produits sous le seuil (nom, quantité, seuil).</p>
                </div>
                <div class="step-body">
                    <h3>Distributions</h3>
                    <p>Lots reçus du fournisseur par statut : en attente, acceptés, rejetés.</p>
                </div>
                <div class="step-body">
                    <h3>Versements</h3>
                    <p>Versements effectués au fournisseur : en attente de validation, validés, rejetés.</p>
                </div>
                <div class="step-body">
                    <h3>Top produits</h3>
                    <p>5 produits les plus vendus dans cette boucherie.</p>
                </div>
                <div class="step-body">
                    <h3>Top clients</h3>
                    <p>5 clients avec le plus d'achats (nombre de ventes + montant total).</p>
                </div>
            </div>
        </div>

        <!-- FOURNISSEUR -->
        <div>
            <div style="display:flex;align-items:center;gap:.75rem;margin-bottom:1rem;">
                <span class="badge badge-fourn">🐄 Fournisseur</span>
                <span style="font-weight:600;font-size:1rem;">Suivi des créances</span>
            </div>
            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:.75rem;">
                <div class="step-body">
                    <h3>Abattages</h3>
                    <p>Nombre d'abattages, poids total de carcasse (kg) et rendement moyen (%) sur la période.</p>
                </div>
                <div class="step-body">
                    <h3>Distributions</h3>
                    <p>Lots envoyés aux boucheries par statut : en attente, acceptés, rejetés.</p>
                </div>
                <div class="step-body">
                    <h3>Versements</h3>
                    <p>Montants reçus par statut + <strong>total dû</strong> (en_attente + validé) vs <strong>total perçu</strong> (validé uniquement).</p>
                </div>
                <div class="step-body">
                    <h3>Top boucheries clientes</h3>
                    <p>5 boucheries ayant reçu le plus de distributions (nombre et quantité totale).</p>
                </div>
            </div>
        </div>

        <div class="info-box" style="margin-top:1.25rem;">
            <strong>Exemple de requête :</strong><br>
            <code>GET /api/v1/stats?periode=semaine</code><br>
            <code>Authorization: Bearer &lt;token&gt;</code>
        </div>
    </section>


    <!-- ══════════════════════════════════════════
         NOUVEAUTÉS V2
    ══════════════════════════════════════════ -->
    <section class="section" id="v2">
        <div class="section-header">
            <span class="badge badge-v2">🆕 v2</span>
            <h2 class="section-title">Nouveautés v2 — Flux par catégorie</h2>
        </div>

        <div class="info-box" style="border-left-color:var(--v2);margin-bottom:1.5rem;">
            <strong>Principe :</strong> Le v2 introduit un flux basé sur les <strong>catégories de viande</strong> (viande_rouge, abats, volaille…) plutôt que sur des produits spécifiques. Les deux flux coexistent — aucune donnée v1 n'est supprimée.
        </div>

        <!-- NOUVELLES TABLES -->
        <div style="margin-bottom:2rem;">
            <h3 style="font-size:1.05rem;font-weight:700;margin-bottom:1rem;color:var(--v2);">Nouvelles tables</h3>
            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:.75rem;">
                <div class="step-body">
                    <h3><code>abattage_lignes</code></h3>
                    <p>Décompose chaque abattage par catégorie. Renseigner <code>lignes</code> dans le corps de <code>POST /abattages</code>.</p>
                </div>
                <div class="step-body">
                    <h3><code>distribution_lignes</code></h3>
                    <p>Quantités distribuées par catégorie vers une boucherie. Renseigner <code>lignes</code> dans <code>POST /distributions</code>.</p>
                </div>
                <div class="step-body">
                    <h3><code>reception_lignes</code></h3>
                    <p>Détail de la réception par catégorie. Renseigner <code>lignes</code> dans <code>POST /receptions</code>.</p>
                </div>
                <div class="step-body">
                    <h3><code>stocks_categories</code></h3>
                    <p>Stock physique en kg par catégorie et par boucherie. Alimenté automatiquement à la réception, décrémenté à la recette journalière.</p>
                </div>
                <div class="step-body">
                    <h3><code>recettes_journalieres</code></h3>
                    <p>Récapitulatif journalier : total des ventes + versement au fournisseur en un seul enregistrement.</p>
                </div>
                <div class="step-body">
                    <h3><code>recette_lignes</code></h3>
                    <p>Détail de la recette par catégorie : kg vendus, prix/kg, montant. Permet le suivi physique et financier.</p>
                </div>
            </div>
        </div>

        <!-- NOUVEAUX ENDPOINTS -->
        <div style="margin-bottom:2rem;">
            <h3 style="font-size:1.05rem;font-weight:700;margin-bottom:1rem;color:var(--v2);">Nouveaux endpoints — Recettes journalières</h3>
            <table class="cred-table">
                <thead>
                    <tr>
                        <th>Méthode</th>
                        <th>Route</th>
                        <th>Rôles</th>
                        <th>Description</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><span class="method get" style="font-weight:700;">GET</span></td>
                        <td><code>/api/v1/recettes</code></td>
                        <td>admin · boucher · fournisseur</td>
                        <td>Liste des recettes (filtrée par rôle)</td>
                    </tr>
                    <tr>
                        <td><span class="method post" style="font-weight:700;">POST</span></td>
                        <td><code>/api/v1/recettes</code></td>
                        <td>boucher · admin</td>
                        <td>Enregistrer la recette du jour + versement</td>
                    </tr>
                    <tr>
                        <td><span class="method get" style="font-weight:700;">GET</span></td>
                        <td><code>/api/v1/recettes/{id}</code></td>
                        <td>admin · boucher · fournisseur</td>
                        <td>Détail d'une recette</td>
                    </tr>
                    <tr>
                        <td><span class="method patch" style="font-weight:700;">PATCH</span></td>
                        <td><code>/api/v1/recettes/{id}/valider</code></td>
                        <td>fournisseur · admin</td>
                        <td>Valider le versement déclaré</td>
                    </tr>
                    <tr>
                        <td><span class="method patch" style="font-weight:700;">PATCH</span></td>
                        <td><code>/api/v1/recettes/{id}/rejeter</code></td>
                        <td>fournisseur · admin</td>
                        <td>Rejeter le versement (montant incorrect…)</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- DEUX SUIVIS -->
        <div>
            <h3 style="font-size:1.05rem;font-weight:700;margin-bottom:1rem;color:var(--v2);">Double suivi automatique</h3>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:.75rem;">
                <div class="step-body" style="border-left:4px solid var(--v2);">
                    <h3>📦 Suivi physique (kg)</h3>
                    <p>Le champ <code>poids_kg_disponible</code> dans <code>stocks_categories</code> est mis à jour automatiquement :</p>
                    <ul style="margin-top:.5rem;padding-left:1.2rem;font-size:.88rem;color:#475569;">
                        <li><strong>+</strong> à la réception d'une distribution (lignes)</li>
                        <li><strong>−</strong> à l'enregistrement d'une recette journalière</li>
                    </ul>
                </div>
                <div class="step-body" style="border-left:4px solid var(--v2);">
                    <h3>💰 Suivi financier</h3>
                    <p>Par recette journalière :</p>
                    <ul style="margin-top:.5rem;padding-left:1.2rem;font-size:.88rem;color:#475569;">
                        <li><code>montant_total</code> = Σ (kg vendus × prix/kg)</li>
                        <li><code>montant_verse</code> = ce que la boucherie reverse</li>
                        <li>Différence = trésorerie retenue</li>
                        <li>Statut : <strong>en_attente → valide / rejete</strong></li>
                    </ul>
                </div>
            </div>
        </div>
    </section>


    <!-- ══════════════════════════════════════════
         COMPTES DE TEST
    ══════════════════════════════════════════ -->
    <section class="section" id="comptes">
        <div class="section-header">
            <span class="badge" style="background:#334155;color:#fff;">🔑 Test</span>
            <h2 class="section-title">Comptes de test</h2>
        </div>

        <table class="cred-table">
            <thead>
                <tr>
                    <th>Rôle</th>
                    <th>Email</th>
                    <th>Mot de passe</th>
                    <th>Boucherie</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><span class="badge badge-admin" style="font-size:.78rem;">admin</span></td>
                    <td><code>admin@test.com</code></td>
                    <td><code>password</code></td>
                    <td>Boucherie Test</td>
                </tr>
                <tr>
                    <td><span class="badge badge-boucher" style="font-size:.78rem;">boucher</span></td>
                    <td><code>boucher@test.com</code></td>
                    <td><code>password</code></td>
                    <td>Boucherie Test</td>
                </tr>
                <tr>
                    <td><span class="badge badge-fourn" style="font-size:.78rem;">fournisseur</span></td>
                    <td><code>fournisseur@test.com</code></td>
                    <td><code>password</code></td>
                    <td>Élevage Test</td>
                </tr>
            </tbody>
        </table>

        <div class="info-box" style="margin-top:1.25rem;">
            <strong>Étape 1 :</strong> Connectez-vous via <code>POST /api/v1/auth/login</code> avec les identifiants ci-dessus.<br>
            <strong>Étape 2 :</strong> Copiez le <code>token</code> retourné dans la réponse.<br>
            <strong>Étape 3 :</strong> Ajoutez le header <code>Authorization: Bearer &lt;token&gt;</code> à toutes vos requêtes suivantes.
        </div>
    </section>

</main>

</body>
</html>
