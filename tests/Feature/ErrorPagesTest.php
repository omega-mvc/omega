<?php

declare(strict_types=1);

afterEach(function (): void {
    restore_error_handler();
    restore_exception_handler();
});

    it('renders every HTTP error page template in the pages directory', function (): void {
        $this->get('/');

        /** @var Closure(string, array): Response $render */
        $render = $this->app->make('view.response');

    $pages = [
        400 => '400 | Bad Request',
        401 => '401 | Unauthorized',
        403 => '403 | Forbidden',
        404 => '404 | Page not found',
        405 => '405 | Method Not Allow',
        429 => '429 | Too Many Request',
        500 => '500 | Internal Server Error',
        503 => '503 | Service Unavailable',
    ];

    foreach ($pages as $code => $marker) {
        $response = $render("pages/{$code}", []);
        $response->setResponseCode($code);

        $this->assertSame($code, $response->getStatusCode());
        $this->assertStringContainsString($marker, $response->getContent());
    }
});
