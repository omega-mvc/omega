<?php

declare(strict_types=1);

use function Omega\Environment\env;

return [
    'redis' => [
        'default'     => env('REDIS_CONNECTION', 'default'),
        'connections' => [
            'default' => [
                'host'       => env('REDIS_HOST', '127.0.0.1'),
                'port'       => (int) env('REDIS_PORT', 6379),
                'password'   => env('REDIS_PASSWORD', null),
                'database'   => (int) env('REDIS_DB', 0),
                'timeout'    => (float) env('REDIS_TIMEOUT', 0.0),
                'persistent' => false,
            ],
            'cache' => [
                'host'     => env('REDIS_HOST', '127.0.0.1'),
                'port'     => (int) env('REDIS_PORT', 6379),
                'password' => env('REDIS_PASSWORD', null),
                'database' => 1,
                'timeout'  => (float) env('REDIS_TIMEOUT', 0.0),
            ],
        ],
    ],
];
