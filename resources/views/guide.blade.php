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
        .section-title { font-size: 1.4rem; font-weight: 700; }

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
</header>

<nav class="pills">
    <a class="pill pill-global"  href="#global">Vue globale</a>
    <a class="pill pill-admin"   href="#admin">Administrateur</a>
    <a class="pill pill-boucher" href="#boucher">Boucher</a>
    <a class="pill pill-fourn"   href="#fournisseur">Fournisseur</a>
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
                        <li>Réceptionne les lots (stock auto-alimenté)</li>
                        <li>Gère le stock</li>
                        <li>Crée les ventes</li>
                        <li>Encaisse les paiements clients</li>
                        <li>Effectue les versements au fournisseur</li>
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
                        La création d'un fournisseur génère automatiquement l'entité fournisseur associée. Un boucher ou admin doit obligatoirement avoir un <code>boucherie_id</code>.
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
                    <p>Le boucher confirme la réception d'un lot distribué par le fournisseur. <strong>Le stock est automatiquement alimenté</strong> et un mouvement d'entrée est enregistré.</p>
                    <span class="endpoint"><span class="method post">POST</span>/api/v1/receptions</span>
                    <br><span style="font-size:.82rem;color:#64748b;margin-top:.3rem;display:block">
                        Corps : { distribution_id, quantite_recue, date_reception }
                    </span>
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
                    <h3>Créer une vente</h3>
                    <p>Enregistre une vente avec les lignes de produits. <strong>Le stock est automatiquement décrémenté.</strong></p>
                    <span class="endpoint"><span class="method post">POST</span>/api/v1/ventes</span>
                    <br><span style="font-size:.82rem;color:#64748b;margin-top:.3rem;display:block">
                        Corps : { client_id, type_vente, lignes: [{ produit_id, quantite, prix_unitaire }] }
                    </span>
                </div>
            </div>
            <div class="arrow"></div>

            <div class="step">
                <div class="step-num num-boucher">7</div>
                <div class="step-body">
                    <h3>Enregistrer le paiement client</h3>
                    <p>Enregistre le règlement du client pour une vente donnée (espèces, mobile money…).</p>
                    <span class="endpoint"><span class="method post">POST</span>/api/v1/ventes/{id}/paiements</span>
                </div>
            </div>
            <div class="arrow"></div>

            <div class="step">
                <div class="step-num num-boucher">8</div>
                <div class="step-body">
                    <h3>Effectuer un versement au fournisseur</h3>
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
                    <br><span style="font-size:.82rem;color:#64748b;margin-top:.3rem;display:block">
                        Corps : { animal_id, date_abattage, poids_carcasse_kg, notes? }
                    </span>
                </div>
            </div>
            <div class="arrow"></div>

            <div class="step">
                <div class="step-num num-fourn">4</div>
                <div class="step-body">
                    <h3>Distribuer les lots aux boucheries</h3>
                    <p>Depuis un abattage, le fournisseur crée des distributions vers une ou plusieurs boucheries en précisant le produit (type de coupe) et la quantité allouée.</p>
                    <span class="endpoint"><span class="method post">POST</span>/api/v1/distributions</span>
                    <br><span style="font-size:.82rem;color:#64748b;margin-top:.3rem;display:block">
                        Corps : { abattage_id, boucherie_id, produit_id, quantite, notes? }
                    </span>
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
