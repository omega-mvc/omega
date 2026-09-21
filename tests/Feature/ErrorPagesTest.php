<?php

declare(strict_types=1);

use Omega\Exceptions\ExceptionHandler;
use Omega\Http\Exceptions\HttpException;
use Omega\Http\Request;

afterEach(function (): void {
    restore_error_handler();
    restore_exception_handler();
});

beforeEach(function (): void {
    $this->get('/');
});

it('renders the :page error page through the exception handler', function (
    int $statusCode,
    string $marker,
): void {
    $handler = $this->app->make(ExceptionHandler::class);

    $response = $handler->render(
        new Request('/'),
        new HttpException($statusCode, 'Error page test.'),
    );

    expect($response->getStatusCode())->toBe($statusCode);
    expect($response->getContent())->toContain($marker);
})->with([
    '400' => [400, '400 | Bad Request'],
    '401' => [401, '401 | Unauthorized'],
    '403' => [403, '403 | Forbidden'],
    '404' => [404, '404 | Page not found'],
    '405' => [405, '405 | Method Not Allow'],
    '429' => [429, '429 | Too Many Request'],
    '500' => [500, '500 | Internal Server Error'],
    '503' => [503, '503 | Service Unavailable'],
]);