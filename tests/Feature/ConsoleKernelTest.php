<?php

declare(strict_types=1);

use App\Kernel\ConsoleKernel;
use Omega\Application\Bootstrapper\BootProviders;
use Omega\Application\Bootstrapper\RegisterProviders;
use Omega\Config\Bootstrapper\ConfigBootstrapper;
use Omega\Console\ConsoleApplication;
use Omega\Facade\Bootstrapper\FacadeBootstrapper;
use Whoops\Handler\PlainTextHandler;
use Whoops\Run;

it('registers the whoops error handler when the app runs in debug mode', function (): void {
    $kernel = $this->app->make(ConsoleApplication::class);

    $this->app->bootstrapWith([
        ConfigBootstrapper::class,
        FacadeBootstrapper::class,
        RegisterProviders::class,
    ]);

    // Set here rather than inherited from APP_DEBUG in the environment, the way the
    // companion test below sets it to false. WhoopsServiceProvider::boot() binds
    // "error.handle" only when the application is in debug mode, so a suite run with
    // APP_DEBUG=false used to fail on a test that is about debug mode.
    $this->app->set('app.debug', true);

    $this->app->bootProvider();

    expect($kernel)->toBeInstanceOf(ConsoleKernel::class);

    $run = $this->app->get('error.handle');

    expect($run)->toBeInstanceOf(Run::class);
    expect($run->getHandlers())->toHaveCount(1);
    expect($run->getHandlers()[0])->toBeInstanceOf(PlainTextHandler::class);
});

it('skips the whoops registration when the app is not in debug mode', function (): void {
    $kernel = $this->app->make(ConsoleApplication::class);

    $this->app->bootstrapWith([
        ConfigBootstrapper::class,
        FacadeBootstrapper::class,
        RegisterProviders::class,
    ]);

    $this->app->set('app.debug', false);

    $this->app->bootProvider();

    expect($kernel)->toBeInstanceOf(ConsoleKernel::class);
    expect($this->app->bound('error.handle'))->toBeFalse();
});