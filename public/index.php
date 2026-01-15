<?php

use Omega\Application\Application;
use Omega\Container\Exceptions\BindingResolutionException;
use Omega\Container\Exceptions\CircularAliasException;
use Omega\Container\Exceptions\EntryNotFoundException;
use Omega\Http\Http;
use Omega\Http\RequestFactory;

if (file_exists($maintenance = dirname(__DIR__) . '/storage/app/maintenance.php')) {
    require $maintenance;
}

require_once dirname(__DIR__) . '/vendor/autoload.php';

/**
 * Application instance.
 *
 * @var Application $app
 */
$app = require_once dirname(__DIR__) . '/bootstrap/app.php';

/**
 * Declare http kernel.
 *
 * @var Http $kernel
 */
try {
    $kernel = $app->make(Http::class);
} catch (CircularAliasException|BindingResolutionException|EntryNotFoundException|ReflectionException $e) {
}


/**
 * Handle Response from HttpKernel.
 */
$response = $kernel->handle(
    $request = new RequestFactory()->getFromGlobal()
)->send();

$kernel->terminate($request, $response);
