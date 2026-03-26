<?php

declare(strict_types=1);

use App\Kernel\ConsoleKernel;
use App\Kernel\HttpKernel;
use Omega\Application\Application;
use Omega\Console\Console;
use Omega\Exceptions\ExceptionHandler;
use Omega\Http\Http;
use Omega\Support\Env;

Env::load(dirname(__DIR__));

try {
    $app = new Application(dirname(__DIR__));

    $app->set(Http::class, fn() => new HttpKernel($app));
    $app->set(Console::class, fn () => new ConsoleKernel($app));
    $app->set(ExceptionHandler::class, fn () => new ExceptionHandler($app));

    return $app;
} catch (Exception $e) {
}
