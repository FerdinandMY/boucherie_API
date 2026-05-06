<?php

return [
    'type'  => 'static',
    'theme' => 'default',

    'title'       => 'Boucherie API — Documentation',
    'description' => 'API REST de gestion de boucherie : stocks, ventes, abattages, paiements, livraisons.',
    'base_url'    => null,
    'logo'        => false,

    'routes' => [
        [
            'match' => [
                'prefixes' => ['api/v1/*'],
                'domains'  => ['*'],
                'versions' => ['v1'],
            ],
            'include' => [],
            'exclude' => [],
            'apply'   => [
                'headers' => [
                    'Accept'       => 'application/json',
                    'Content-Type' => 'application/json',
                ],
                'response_calls' => [
                    'methods' => [],
                ],
            ],
        ],
    ],

    'auth' => [
        'enabled'      => true,
        'default'      => true,
        'in'           => 'bearer',
        'name'         => 'Authorization',
        'use_value'    => env('SCRIBE_AUTH_KEY', 'votre-token-sanctum'),
        'placeholder'  => '{TOKEN}',
        'extra_info'   => 'Obtenez un token via `POST /api/v1/auth/login`. Passez-le en header : `Authorization: Bearer {TOKEN}`.',
    ],

    'intro_text' => <<<INTRO
Cette documentation couvre l'ensemble des endpoints de l'API Boucherie.

## Authentification

L'API utilise **Laravel Sanctum** (tokens Bearer). Toutes les routes (sauf `/auth/login` et `/auth/register`) nécessitent un token.

## Rôles

| Rôle      | Accès |
|-----------|-------|
| `admin`   | Tout (boucheries, utilisateurs, …) |
| `boucher` | Fournisseurs, animaux, abattages, stocks, ventes |
| `caissier`| Ventes, paiements, livraisons |

## Codes d'erreur courants

| Code | Signification |
|------|---------------|
| 401  | Non authentifié |
| 403  | Rôle insuffisant |
| 422  | Erreur de validation |
| 404  | Ressource introuvable |
INTRO,

    'try_it_out' => [
        'enabled'  => true,
        'base_url' => env('APP_URL'),
        'use_csrf' => false,
    ],

    'groups' => [
        'default'      => 'Endpoints',
        'order'        => [
            'Authentification',
            'Référentiels (Enums)',
            'Boucheries',
            'Utilisateurs',
            'Fournisseurs',
            'Clients',
            'Produits',
            'Achats Fournisseurs',
            'Animaux',
            'Abattages',
            'Stocks',
            'Ventes',
            'Paiements',
            'Livraisons',
        ],
    ],

    'examples' => [
        'faker_seed'       => 1234,
        'models_source'    => ['factoryCreate', 'factoryMake', 'databaseFirst'],
    ],

    'fractal'         => ['envelop_key' => null, 'serializer' => null],
    'routeMatcher'    => \Knuckles\Scribe\Matching\RouteMatcher::class,
    'strategies'      => [
        'metadata'            => [\Knuckles\Scribe\Extracting\Strategies\Metadata\GetFromDocBlocks::class],
        'urlParameters'       => [\Knuckles\Scribe\Extracting\Strategies\UrlParameters\GetFromLaravelAPI::class, \Knuckles\Scribe\Extracting\Strategies\UrlParameters\GetFromUrlParamTag::class],
        'queryParameters'     => [\Knuckles\Scribe\Extracting\Strategies\QueryParameters\GetFromFormRequest::class, \Knuckles\Scribe\Extracting\Strategies\QueryParameters\GetFromQueryParamTag::class],
        'headers'             => [\Knuckles\Scribe\Extracting\Strategies\Headers\GetFromRouteRules::class, \Knuckles\Scribe\Extracting\Strategies\Headers\GetFromHeaderTag::class],
        'bodyParameters'      => [\Knuckles\Scribe\Extracting\Strategies\BodyParameters\GetFromFormRequest::class, \Knuckles\Scribe\Extracting\Strategies\BodyParameters\GetFromBodyParamTag::class],
        'responses'           => [\Knuckles\Scribe\Extracting\Strategies\Responses\UseTransformerTags::class, \Knuckles\Scribe\Extracting\Strategies\Responses\UseApiResourceTags::class, \Knuckles\Scribe\Extracting\Strategies\Responses\UseResponseTag::class, \Knuckles\Scribe\Extracting\Strategies\Responses\UseResponseFileTag::class],
        'responseFields'      => [\Knuckles\Scribe\Extracting\Strategies\ResponseFields\GetFromResponseFieldTag::class],
    ],

    'postman' => [
        'enabled'      => true,
        'overrides'    => [],
    ],

    'openapi' => [
        'enabled'    => true,
        'overrides'  => [],
    ],

    'static' => [
        'output_path' => 'public/docs',
    ],

    'laravel' => [
        'add_routes'       => true,
        'docs_url'         => '/docs',
        'assets_directory' => null,
        'middleware'        => [],
    ],
];
