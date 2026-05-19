# Butcher Backend API

API REST Laravel 10 pour la gestion d'une boucherie : achats fournisseurs, abattages, stocks, ventes, distributions et versements.

## Stack technique

- **PHP 8.4** — `declare(strict_types=1)` partout
- **Laravel 10** — architecture Controller → Service → Repository
- **Laravel Sanctum** — authentification par token Bearer
- **Spatie Laravel Permission** — gestion des rôles (admin, boucher, fournisseur)
- **PostgreSQL** (production Render) / **MySQL** (local) / **SQLite** (tests)
- **Pest 2** — tests unitaires et d'intégration
- **Docker** — build multi-stage avec gate de tests avant déploiement

---

## Installation locale

```bash
git clone <repo>
cd butcher-backend-app

composer install

cp .env.example .env
php artisan key:generate

# Configurer la base de données dans .env, puis :
php artisan migrate --seed

php artisan serve
```

### Variables d'environnement obligatoires

| Variable | Description | Exemple |
|---|---|---|
| `APP_KEY` | Clé de chiffrement Laravel | généré par `artisan key:generate` |
| `DB_CONNECTION` | Driver BDD | `mysql` / `pgsql` |
| `DB_HOST` | Hôte BDD | `127.0.0.1` |
| `DB_DATABASE` | Nom de la BDD | `butcher_db` |
| `DB_USERNAME` | Utilisateur BDD | `root` |
| `DB_PASSWORD` | Mot de passe BDD | — |

### Variables d'environnement optionnelles (sécurité)

| Variable | Description | Défaut |
|---|---|---|
| `ALLOWED_ORIGINS` | Origines CORS autorisées, séparées par virgule | `http://localhost:3000,http://localhost:8000` |
| `SANCTUM_TOKEN_EXPIRATION` | Durée de vie des tokens en minutes | `43200` (30 jours) |

> **Note — application mobile :** CORS est un mécanisme de sécurité navigateur uniquement. Pour une API consommée exclusivement par une app mobile, `ALLOWED_ORIGINS` n'a pas d'impact sécurité. La protection repose sur l'authentification Sanctum, le rate limiting et la validation des entrées.

---

## Rôles et permissions

| Rôle | Accès |
|---|---|
| `admin` | Accès total — gestion utilisateurs, stats globales, toutes les ressources |
| `boucher` | Gestion de sa boucherie — ventes, stocks, clients, fournisseurs |
| `fournisseur` | Ses achats, abattages, distributions, versements |

**Important :** les rôles sont attribués uniquement via `POST /api/v1/users` (admin uniquement). L'endpoint public `/api/v1/auth/register` ne permet pas de choisir un rôle.

---

## Authentification

Toutes les routes (sauf `/auth/register` et `/auth/login`) requièrent un header :

```
Authorization: Bearer <token>
```

Le token est retourné à la connexion (`POST /api/v1/auth/login`) et à l'inscription (`POST /api/v1/auth/register`).

### Expiration des tokens

Les tokens expirent après **30 jours** (configurable via `SANCTUM_TOKEN_EXPIRATION`). À l'expiration, le client doit se reconnecter.

### Rate limiting

- `POST /api/v1/auth/login` : **10 tentatives par minute** par IP (protection brute-force)
- Toutes les autres routes : 60 requêtes par minute (middleware `throttle:api`)

---

## Routes principales

```
POST   /api/v1/auth/register
POST   /api/v1/auth/login
POST   /api/v1/auth/logout          [auth]
GET    /api/v1/auth/me              [auth]

GET    /api/v1/users                [admin]
POST   /api/v1/users                [admin]
GET    /api/v1/users/{id}           [admin]
PUT    /api/v1/users/{id}           [admin]
DELETE /api/v1/users/{id}           [admin]

GET    /api/v1/boucheries           [admin|boucher|fournisseur]
POST   /api/v1/boucheries           [admin|boucher]

GET    /api/v1/fournisseurs         [admin|boucher]
POST   /api/v1/fournisseurs         [admin|boucher]

GET    /api/v1/achats-fournisseurs  [admin|boucher|fournisseur]
POST   /api/v1/achats-fournisseurs  [admin|boucher|fournisseur]

GET    /api/v1/abattages            [admin|boucher|fournisseur]
POST   /api/v1/abattages            [admin|boucher|fournisseur]

GET    /api/v1/stocks               [admin|boucher]
POST   /api/v1/stocks/{id}/ajuster  [admin|boucher]

GET    /api/v1/ventes               [admin|boucher]
POST   /api/v1/ventes               [admin|boucher]
PATCH  /api/v1/ventes/{id}/statut   [admin|boucher]

GET    /api/v1/distributions        [admin|boucher|fournisseur]
POST   /api/v1/distributions        [admin|fournisseur]

GET    /api/v1/versements           [admin|boucher|fournisseur]
POST   /api/v1/versements           [admin|boucher]

GET    /api/v1/stats/admin          [admin]
GET    /api/v1/stats/boucher        [admin|boucher]
GET    /api/v1/stats/fournisseur    [admin|fournisseur]
```

La documentation Scribe complète (avec exemples de requêtes/réponses) est disponible sur `/docs` après `php artisan scribe:generate`.

---

## Architecture

```
app/
├── Http/
│   ├── Controllers/Api/V1/   Contrôleurs — thin, délèguent aux services
│   ├── Requests/             Validation des entrées (FormRequest)
│   ├── Resources/            Formatage des sorties (JsonResource)
│   └── Middleware/
├── Services/                 Logique métier
├── Repositories/             Accès base de données
├── Models/                   Eloquent
└── Policies/                 Contrôle d'accès par ressource
```

Chaque requête suit le flux : `Request → Middleware (auth + rôle) → Controller → Service → Repository → Response`

---

## Tests

```bash
# Lancer tous les tests
php vendor/bin/pest --no-coverage

# Lancer un fichier spécifique
php vendor/bin/pest tests/Feature/V1/VenteTest.php
```

Les tests utilisent SQLite in-memory (`RefreshDatabase` à chaque test). Les rôles sont créés dans `tests/Pest.php` via `beforeEach` chaîné à `uses()`.

### Structure des tests

```
tests/
├── Pest.php              Configuration globale + helpers (adminUser, boucherUser, fournisseurUser)
├── Feature/
│   ├── Auth/AuthTest.php
│   └── V1/               Tests d'intégration par ressource
└── Unit/
    └── Services/         Tests unitaires des services
```

---

## Déploiement (Render)

Le déploiement passe par un **build Docker multi-stage** défini dans [Dockerfile](Dockerfile) :

1. **Stage `composer`** — installation des dépendances PHP
2. **Stage `tester`** — exécution de `pest --no-coverage` (gate de qualité)
3. **Stage `production`** — image finale si tous les tests passent

Si un test échoue, le build Docker s'arrête et le déploiement Render est bloqué.

Variables d'environnement à configurer dans le dashboard Render :

```
APP_ENV=production
APP_KEY=<générer avec artisan key:generate>
APP_URL=https://votre-app.onrender.com
DB_CONNECTION=pgsql
DB_HOST=<host Render PostgreSQL>
DB_DATABASE=<nom BDD>
DB_USERNAME=<user BDD>
DB_PASSWORD=<password BDD>
SANCTUM_TOKEN_EXPIRATION=43200
```

---

## Sécurité

### Mesures en place

| Mécanisme | Détail |
|---|---|
| Authentification | Laravel Sanctum — token Bearer, expiration 30 jours |
| Rôles | Spatie Permission — admin / boucher / fournisseur |
| Rate limiting | 10 req/min sur `/auth/login`, 60 req/min global |
| Validation entrées | FormRequest avec règles strictes sur tous les endpoints |
| Taille password | Max 100 caractères (protection DoS bcrypt) |
| Isolation données | Policies Laravel par ressource (vente, stock, fournisseur…) |
| Calculs serveur | `montant_total` recalculé côté serveur (non manipulable) |
| Host Header | Middleware `TrustHosts` activé |
| CORS | Origines configurables via `ALLOWED_ORIGINS` |

### Ce qui ne s'applique PAS à une API mobile

CORS est un mécanisme de sécurité **navigateur uniquement**. Les apps mobiles (React Native, Flutter, iOS, Android) ne l'appliquent pas. La sécurité d'une API mobile repose sur HTTPS + tokens d'authentification + validation serveur — tout ce qui est déjà en place.

### Création des utilisateurs

- **`POST /api/v1/auth/register`** — inscription publique, **aucun rôle attribué**
- **`POST /api/v1/users`** — création avec rôle, **réservé aux admins**

Ne jamais utiliser `/register` pour créer des comptes avec des privilèges.
