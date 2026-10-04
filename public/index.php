<?php

use Omega\Application\Application;
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
 * A kernel that cannot be built is not handled here. Swallowing the failure left
 * $kernel undefined, so the next line raised "Call to a member function handle()
 * on null" and the reason the kernel could not be built was gone: unreported,
 * unlogged, and replaced by a fatal that names the absolute path of the file it
 * failed in. Letting the failure reach the exception handler keeps the cause.
 *
 * @var Http $kernel
 */
$kernel = $app->make(Http::class);


/**
 * Handle Response from HttpKernel.
 */
$response = $kernel->handle(
    $request = new RequestFactory()->getFromGlobal()
)->send();

$kernel->terminate($request, $response);
