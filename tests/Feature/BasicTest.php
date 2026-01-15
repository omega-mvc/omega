<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\AbstractTestCase;
use Throwable;

final class BasicTest extends AbstractTestCase
{
    /**
     * Test it can see welcome page.
     *
     * @return void
     * @throws Throwable
     */
    public function testItCanSeeWelcomePage(): void
    {
        $this
            ->get('/')
            ->assertOk();
    }

    protected function tearDown(): void
    {
        restore_error_handler();
        restore_exception_handler();
    }
}
