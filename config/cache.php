<?php

declare(strict_types=1);

use function Omega\Environment\env;
use function Omega\Application\get_path;

return [
    'cache' => [
        'default' => env('CACHE_STORAGE', 'file'),
        'multi'   => [],
        'storage' => [
            'file'   => [
                'ttl'  => (int) env('CACHE_SECONDS', '31536000'),
                'path' => get_path('path.cache'),
            ],
            'memory' => [
                'ttl'  => (int) env('CACHE_SECONDS', '31536000'),
            ],
        ],
    ]
];
