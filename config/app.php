<?php

declare(strict_types=1);

use App\Providers\AppServiceProvider;
use App\Providers\DatabaseServiceProvider;
use App\Providers\RouteServiceProvider;
use App\Providers\ViewServiceProvider;
use Omega\Cache\CacheServiceProvider;
use Omega\RateLimiter\RateLimiterServiceProvider;

return [
    'name'        => env('APP_NAME', ''),
    'version'     => env('APP_VERSION', ''),
    'key'         => env('APP_KEY', ''),
    'url'         => env('APP_URL', 'http://localhost'),
    'environment' => env('APP_ENV', 'dev'),
    'debug'       => env('APP_DEBUG', ''),
    'timezone'    => env('APP_TIMEZONE', 'UTC'),
    'providers'   => [
        AppServiceProvider::class,
        RouteServiceProvider::class,
        DatabaseServiceProvider::class,
        ViewServiceProvider::class,
        CacheServiceProvider::class,
        RateLimiterServiceProvider::class,
    ],
];
