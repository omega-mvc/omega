<?php

declare(strict_types=1);

namespace Tests\App;

use Omega\Application\ApplicationInterface;
use Omega\Exceptions\ExceptionHandler;
use Omega\Http\Exceptions\HttpException;
use Omega\Http\Request;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class ErrorPagesTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        /** @var ApplicationInterface $app */
        $app = require dirname(__DIR__, 3) . '/bootstrap/app.php';
        $this->app = $app;

        $this->get('/');
    }

    #[DataProvider('errorPageProvider')]
    public function testRendersTheErrorPageThroughTheExceptionHandler(
        int $statusCode,
        string $marker,
    ): void {
        /** @var ExceptionHandler $handler */
        $handler = $this->app->make(ExceptionHandler::class);

        $response = $handler->render(
            new Request('/'),
            new HttpException($statusCode, 'Error page test.'),
        );

        $this->assertSame($statusCode, $response->getStatusCode());

        // getContent() is string|array; an error page is rendered as a string, so
        // fail on anything else instead of letting assertStringContainsString()
        // blow up on a TypeError if the handler ever handed back an array.
        $content = $response->getContent();
        if (!is_string($content)) {
            $this->fail(sprintf('Expected string content, %s given.', get_debug_type($content)));
        }

        $this->assertStringContainsString($marker, $content);
    }

    #[DataProvider('errorPageProvider')]
    public function testRendersTheStatusCodeErrorPage(
        int $statusCode,
        string $marker,
    ): void {
        /** @var ExceptionHandler $handler */
        $handler = $this->app->make(ExceptionHandler::class);

        $response = $handler->render(
            new Request('/'),
            new HttpException($statusCode, 'Error page test.'),
        );

        $this->assertSame($statusCode, $response->getStatusCode());

        // getContent() is string|array; fail on anything but a string, see above.
        $content = $response->getContent();
        if (!is_string($content)) {
            $this->fail(sprintf('Expected string content, %s given.', get_debug_type($content)));
        }

        $this->assertStringContainsString($marker, $content);
    }

    /**
     * Keys are written as '400', '401', ... but PHP normalises numeric string
     * array keys to int, so the shape is array<int, ...> and not array<string, ...>.
     *
     * @return array<int, array{0: int, 1: string}>
     */
    public static function errorPageProvider(): array
    {
        return [
            '400' => [400, '400 | Bad Request'],
            '401' => [401, '401 | Unauthorized'],
            '403' => [403, '403 | Forbidden'],
            '404' => [404, '404 | Page not found'],
            '405' => [405, '405 | Method Not Allow'],
            '429' => [429, '429 | Too Many Request'],
            '500' => [500, '500 | Internal Server Error'],
            '503' => [503, '503 | Service Unavailable'],
        ];
    }
}
