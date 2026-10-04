<?php

declare(strict_types=1);

it('serves the home page with a successful status code', function (): void {
    $this->get('/')->assertOk();
});

it('renders the demo application title', function (): void {
    $this->get('/')->assertSee('Omega Demo Application');
});

it('renders the welcome message', function (): void {
    $this->get('/')->assertSee('Welcome to Omega!');
});
