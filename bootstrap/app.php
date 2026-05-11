<?php

declare(strict_types=1);

use App\Kernel\ConsoleKernel;
use App\Kernel\HttpKernel;
use Omega\Application\Application;
use Omega\Console\ConsoleApplication;
use Omega\Environment\Env;
use Omega\Exceptions\ExceptionHandler;
use Omega\Http\Http;

Env::load(dirname(__DIR__));

try {
    $app = new Application(dirname(__DIR__));

    $app->set(Http::class, fn() => new HttpKernel($app));
    $app->set(ConsoleApplication::class, fn () => new ConsoleKernel($app));
    $app->set(ExceptionHandler::class, fn () => new ExceptionHandler($app));

    return $app;
} catch (Exception $e) {
}
