<?php

declare(strict_types=1);

namespace Tests;

use Omega\Testing\TestCase;

abstract class AbstractTestCase extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->app = require dirname(__DIR__) . '/bootstrap/app.php';
    }
}
