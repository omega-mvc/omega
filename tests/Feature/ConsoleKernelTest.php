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

afterEach(function (): void {
    if ($this->app->bound('error.handle')) {
        restore_error_handler();
        restore_exception_handler();
    }
});

it('registers the whoops error handler when the app runs in debug mode', function (): void {
    $kernel = $this->app->make(ConsoleApplication::class);

    $this->app->bootstrapWith([
        ConfigBootstrapper::class,
        FacadeBootstrapper::class,
        RegisterProviders::class,
        BootProviders::class,
    ]);

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