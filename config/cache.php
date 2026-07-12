<?php

declare(strict_types=1);

use function Omega\Environment\env;
use function Omega\Application\get_path;

return [
    'cache' => [
        'default' => env('CACHE_STORAGE', 'file'),
        'storage' => [
            'apcu'   => [
                'ttl'        => (int) env('CACHE_SECONDS', '31536000'),
                'prefix'     => env('CACHE_PREFIX', 'omega_'),
            ],
            'file'   => [
                'ttl'        => (int) env('CACHE_SECONDS', '31536000'),
                'path'       => get_path('path.cache'),
            ],
            'memory' => [
                'ttl'        => (int) env('CACHE_SECONDS', '31536000'),
            ],
            'redis'  => [
                'ttl'        => (int) env('CACHE_SECONDS', '31536000'),
                'connection' => env('REDIS_CONNECTION', 'default'),
            ],
        ],
    ]
];
