<?php

declare(strict_types=1);

use App\Services\IndexService;
use Omega\Router\Router;

// register router using attribute
try {
    Router::register([
        IndexService::class,
        // add more class
    ]);
} catch (ReflectionException $e) {

}
