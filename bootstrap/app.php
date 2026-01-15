<?php

declare(strict_types=1);

use Omega\Application\Application;
use Omega\Console\Console;
use Omega\Console\ConsoleError;
use Omega\Exceptions\ExceptionHandler;
use Omega\Http\Http;
use Omega\Http\HttpError;
use Omega\Support\Env;

Env::load(dirname(__DIR__));

try {
    $app = new Application(dirname(__DIR__));

    $app->set(Http::class, fn() => new HttpError($app));
    $app->set(Console::class, fn () => new ConsoleError($app));
    $app->set(ExceptionHandler::class, fn () => new ExceptionHandler($app));

    return $app;
} catch (Exception $e) {
}
