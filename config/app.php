<?php

declare(strict_types=1);

use function Omega\Support\env;

return [
    'name'        => env('APP_NAME', ''),
    'version'     => env('APP_VERSION', ''),
    'key'         => env('APP_KEY', ''),
    'url'         => env('APP_URL', 'http://localhost'),
    'environment' => env('APP_ENV', 'dev'),
    'debug'       => env('APP_DEBUG', ''),
    'timezone'    => env('APP_TIMEZONE', 'UTC'),
];
