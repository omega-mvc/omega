<?php

declare(strict_types=1);

namespace Tests\App;

use Omega\Application\ApplicationInterface;
use Tests\TestCase;

class IndexControllerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        /** @var ApplicationInterface $app */
        $app = require dirname(__DIR__, 3) . '/bootstrap/app.php';
        $this->app = $app;
    }

    public function testServesTheHomePageWithASuccessfulStatusCode(): void
    {
        $this->get('/')->assertOk();
    }

    public function testRendersTheDemoApplicationTitle(): void
    {
        $this->get('/')->assertSee('Omega Demo Application');
    }

    public function testRendersTheWelcomeMessage(): void
    {
        $this->get('/')->assertSee('Welcome to Omega!');
    }
}
