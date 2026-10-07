<?php

declare(strict_types=1);

namespace Tests\App;

use Omega\Application\ApplicationInterface;
use Tests\TestCase;

// NOTA: i tre test qui sotto passano le asserzioni ma PHPUnit li marca RISKY
// ("Test code or tested code did not remove its own error handlers"). La causa non è
// stata ancora isolata e non va indovinato: APP_ENV=testing è già impostato da
// phpunit.xml.dist, quindi l'ipotesi che il bootstrapper degli handler debba
// registrare qualcosa va verificata per conto, non corretta con un workaround qui.

class NotFoundRouteTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        /** @var ApplicationInterface $app */
        $app = require dirname(__DIR__, 3) . '/bootstrap/app.php';
        $this->app = $app;
    }

    public function testAnswersAPathNoRouteMatchesWithTheNotFoundStatus(): void
    {
        // The status travels as the third argument of view(). Handed over inside the view
        // data instead, the page still rendered but the response went out as a plain 200,
        // which is what a crawler and a monitoring system read as a healthy endpoint.
        $this->get('/does-not-exist')->assertStatusCode(404);
    }

    public function testRendersTheNotFoundPageOnAPathNoRouteMatches(): void
    {
        $this->get('/does-not-exist')->assertSee('404 | Page not found');
    }

    public function testLeavesAMatchedRouteOnTheStatusItDeclares(): void
    {
        // The counterpart of the two above: moving the status out of the view data must
        // not disturb the routes that were already answering correctly.
        $this->get('/')->assertStatusCode(200);
    }
}
