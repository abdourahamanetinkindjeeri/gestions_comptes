<?php

return [
    'default' => 'default',

    'documentations' => [
        'default' => [
            'api' => [
                'title' => 'Documentation API - Gestion des Comptes',
            ],

            'routes' => [
                /*
                 * Route d’accès à l’interface de documentation Swagger UI
                 */
                'api' => 'api/documentation',
            ],

            'paths' => [
                /*
                 * Toujours utiliser le chemin absolu pour les assets
                 */
                'use_absolute_path' => true,

                /*
                 * Forcer l’URL des assets Swagger UI en HTTPS via APP_URL
                 */
                'swagger_ui_assets_path' => env(
                    'L5_SWAGGER_UI_ASSETS_PATH',
                    rtrim(env('APP_URL', 'https://gestions-comptes.onrender.com'), '/') . '/docs/asset/'
                ),

                /*
                 * Nom du fichier JSON/YAML généré pour la documentation
                 */
                'docs_json' => 'api-docs.json',
                'docs_yaml' => 'api-docs.yaml',

                /*
                 * Format de doc utilisé par défaut (json ou yaml)
                 */
                'format_to_use_for_docs' => env('L5_FORMAT_TO_USE_FOR_DOCS', 'json'),

                /*
                 * Dossier contenant les annotations Swagger (ex: app/)
                 */
                'annotations' => [
                    base_path('app'),
                ],
            ],
        ],
    ],

    'defaults' => [
        'routes' => [
            'docs' => 'docs',
            'oauth2_callback' => 'api/oauth2-callback',
            'middleware' => [
                'api' => [],
                'asset' => [],
                'docs' => [],
                'oauth2_callback' => [],
            ],
            'group_options' => [],
        ],

        'paths' => [
            'docs' => storage_path('api-docs'),
            'views' => base_path('resources/views/vendor/l5-swagger'),
            'base' => env('L5_SWAGGER_BASE_PATH', null),
            'excludes' => [],
        ],

        'scanOptions' => [
            'default_processors_configuration' => [],
            'analyser' => null,
            'analysis' => null,
            'processors' => [],
            'pattern' => null,
            'exclude' => [],
            'open_api_spec_version' => env(
                'L5_SWAGGER_OPEN_API_SPEC_VERSION',
                \L5Swagger\Generator::OPEN_API_DEFAULT_SPEC_VERSION
            ),
        ],

        'securityDefinitions' => [
            'securitySchemes' => [
                // Exemple pour Laravel Passport
                /*
                'passport' => [
                    'type' => 'oauth2',
                    'description' => 'Sécurité OAuth2 via Laravel Passport',
                    'scheme' => 'https',
                    'flows' => [
                        "password" => [
                            "authorizationUrl" => config('app.url') . '/oauth/authorize',
                            "tokenUrl" => config('app.url') . '/oauth/token',
                            "refreshUrl" => config('app.url') . '/token/refresh',
                            "scopes" => []
                        ],
                    ],
                ],
                'sanctum' => [
                    'type' => 'apiKey',
                    'description' => 'Token Bearer (Authorization: Bearer <token>)',
                    'name' => 'Authorization',
                    'in' => 'header',
                ],
                */
            ],
            'security' => [
                // Exemple :
                // ['passport' => []],
            ],
        ],

        'generate_always' => env('L5_SWAGGER_GENERATE_ALWAYS', false),
        'generate_yaml_copy' => env('L5_SWAGGER_GENERATE_YAML_COPY', false),
        'proxy' => false,
        'additional_config_url' => null,
        'operations_sort' => env('L5_SWAGGER_OPERATIONS_SORT', null),
        'validator_url' => null,

        /*
         * Configuration de Swagger UI (apparence et comportement)
         */
        'ui' => [
            'display' => [
                'dark_mode' => env('L5_SWAGGER_UI_DARK_MODE', false),
                'doc_expansion' => env('L5_SWAGGER_UI_DOC_EXPANSION', 'none'),
                'filter' => env('L5_SWAGGER_UI_FILTERS', true),
            ],
            'authorization' => [
                'persist_authorization' => env('L5_SWAGGER_UI_PERSIST_AUTHORIZATION', true),
                'oauth2' => [
                    'use_pkce_with_authorization_code_grant' => false,
                ],
            ],
        ],

        /*
         * Forcer Swagger UI à utiliser les assets HTTPS
         */
        'ui_custom_assets' => [
            'swagger_ui_css' => env(
                'L5_SWAGGER_UI_CSS',
                'https://gestions-comptes.onrender.com/docs/asset/swagger-ui.css'
            ),
            'swagger_ui_bundle_js' => env(
                'L5_SWAGGER_UI_BUNDLE_JS',
                'https://gestions-comptes.onrender.com/docs/asset/swagger-ui-bundle.js'
            ),
            'swagger_ui_standalone_preset_js' => env(
                'L5_SWAGGER_UI_STANDALONE_PRESET_JS',
                'https://gestions-comptes.onrender.com/docs/asset/swagger-ui-standalone-preset.js'
            ),
        ],

        /*
         * Constantes utilisables dans les annotations
         */
        'constants' => [
            'L5_SWAGGER_CONST_HOST' => env(
                'L5_SWAGGER_CONST_HOST',
                'https://gestions-comptes.onrender.com'
            ),
        ],
    ],
];
