<?php

declare(strict_types=1);

namespace Tests\App;

use App\Kernel\ConsoleKernel;
use Omega\Application\ApplicationInterface;
use Omega\Application\Bootstrapper\BootProviders;
use Omega\Application\Bootstrapper\RegisterProviders;
use Omega\Config\Bootstrapper\ConfigBootstrapper;
use Omega\Console\ConsoleApplication;
use Omega\Facade\Bootstrapper\FacadeBootstrapper;
use Tests\TestCase;
use Whoops\Handler\PlainTextHandler;
use Whoops\Run;

class ConsoleKernelTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        /** @var ApplicationInterface $app */
        $app = require dirname(__DIR__, 3) . '/bootstrap/app.php';
        $this->app = $app;
    }

    public function testRegistersTheWhoopsErrorHandlerWhenTheAppRunsInDebugMode(): void
    {
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

        $this->assertInstanceOf(ConsoleKernel::class, $kernel);

        $run = $this->app->get('error.handle');

        $this->assertInstanceOf(Run::class, $run);
        $this->assertCount(1, $run->getHandlers());
        $this->assertInstanceOf(PlainTextHandler::class, $run->getHandlers()[0]);
    }

    public function testSkipsTheWhoopsRegistrationWhenTheAppIsNotInDebugMode(): void
    {
        $kernel = $this->app->make(ConsoleApplication::class);

        $this->app->bootstrapWith([
            ConfigBootstrapper::class,
            FacadeBootstrapper::class,
            RegisterProviders::class,
        ]);

        $this->app->set('app.debug', false);

        $this->app->bootProvider();

        $this->assertInstanceOf(ConsoleKernel::class, $kernel);
        $this->assertFalse($this->app->bound('error.handle'));
    }
}
