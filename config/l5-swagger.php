<?php

return [
    'api' => [
        'title' => 'Dictionary API',
        'description' => 'A RESTful API for dictionary management with user authentication and word lookup',
        'version' => '1.0.0',
        'basePath' => '/api',
        'schemes' => ['http', 'https'],
        'consumes' => ['application/json'],
        'produces' => ['application/json'],
    ],

    'routes' => [
        'api' => 'api/swagger',
        'docs' => 'docs',
        'oauth2_callback' => 'api/oauth2-callback',
        'middleware' => [
            'api' => [],
            'asset' => [],
            'docs' => [],
            'oauth2_callback' => [],
        ],
        'group' => 'api',
    ],

    'paths' => [
        'docs' => storage_path('api-docs'),
        'docs_json' => 'api-docs.json',
        'docs_yaml' => 'api-docs.yaml',
        'annotations' => [
            base_path('app'),
        ],
        'exclude' => [
            base_path('app/Http/Controllers/Auth'),
        ],
    ],

    'security' => [
        'bearerAuth' => [
            'type' => 'http',
            'scheme' => 'bearer',
            'bearerFormat' => 'JWT',
        ],
    ],

    'swagger_ui' => [
        'display' => [
            'doc_expansion' => 'none',
            'filter' => true,
            'operationsSorter' => 'alpha',
            'tagsSorter' => 'alpha',
            'defaultModelsExpandDepth' => 3,
            'defaultModelExpandDepth' => 3,
            'showCommonExtensions' => true,
            'showExtensions' => true,
            'showRequestHeaders' => true,
            'showResponseHeaders' => true,
        ],
    ],

    'constants' => [
        'L5_SWAGGER_CONST_HOST' => env('L5_SWAGGER_CONST_HOST', 'http://localhost:8001'),
    ],
];
