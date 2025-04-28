<?php

return [
    'paths' => ['*', 'api/*', 'sanctum/csrf-cookie', 'api/documentation', 'docs/*', 'oauth2-callback', 'api-docs/*'],

    'allowed_methods' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'],

    'allowed_origins' => ['*'],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => ['x-cache', 'x-response-time'],

    'max_age' => 86400,

    'supports_credentials' => false,

    'paths_ignore' => [],
];
