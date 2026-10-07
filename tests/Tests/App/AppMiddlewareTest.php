<?php

declare(strict_types=1);

namespace Tests\App;

use App\Middlewares\AppMiddleware;
use Omega\Application\ApplicationInterface;
use Omega\Http\Request;
use Omega\Http\Response;
use Tests\TestCase;

class AppMiddlewareTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        /** @var ApplicationInterface $app */
        $app = require dirname(__DIR__, 3) . '/bootstrap/app.php';
        $this->app = $app;
    }

    public function testHandsTheRequestToTheNextLayerAndReturnsItsResponse(): void
    {
        $middleware = new AppMiddleware();
        $request    = new Request('/');
        $downstream = new Response('downstream', 201);

        $seen     = null;
        $response = $middleware->handle(
            $request,
            static function (Request $next) use (&$seen, $downstream): Response {
                $seen = $next;

                return $downstream;
            }
        );

        $this->assertSame($request, $seen);
        $this->assertSame($downstream, $response);
        $this->assertSame(201, $response->getStatusCode());
    }
}
