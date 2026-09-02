<?php

declare(strict_types=1);

afterEach(function (): void {
    restore_error_handler();
    restore_exception_handler();
});

it('serves the home page with a successful status code', function (): void {
    $this->get('/')->assertOk();
});

it('renders the demo application title', function (): void {
    $this->get('/')->assertSee('Omega Demo Application');
});

it('renders the welcome message', function (): void {
    $this->get('/')->assertSee('Welcome to Omega!');
});
