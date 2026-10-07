<?php

declare(strict_types=1);

namespace Tests;

use Omega\Testing\TestCase as OmegaTestCase;
use Whoops\Run;

use function get_exception_handler;
use function is_array;
use function restore_error_handler;
use function restore_exception_handler;
use function set_error_handler;

/**
 * Shared base class for the app suites.
 *
 * The kernels register the Whoops error/exception handlers while the app is
 * booted, inside the test itself. PHPUnit snapshots the handler stack before
 * every test (before setUp) and re-checks it after tearDown: any handler left
 * on the stack marks the test as risky. This class pops the Whoops handlers
 * off both stacks around each test; PHPUnit's own handlers are never touched
 * because only handlers targeting a Whoops\Run instance are removed.
 */
class TestCase extends OmegaTestCase
{
    /**
     * Deregister the handlers left on the stack by the previous test, before
     * this test boots the application again.
     */
    protected function setUp(): void
    {
        $this->deregisterWhoopsHandlers();

        parent::setUp();
    }

    /**
     * Deregister the handlers this test registered, before PHPUnit compares
     * the stack against the snapshot it took before setUp.
     */
    protected function tearDown(): void
    {
        $this->deregisterWhoopsHandlers();

        parent::tearDown();
    }

    /**
     * Pop every Whoops handler sitting on top of the error and exception
     * handler stacks.
     */
    private function deregisterWhoopsHandlers(): void
    {
        $probe = static fn (): bool => false;

        while (true) {
            $current = set_error_handler($probe);
            restore_error_handler();

            if (!is_array($current) || !($current[0] instanceof Run)) {
                break;
            }

            restore_error_handler();
        }

        while (true) {
            $current = get_exception_handler();

            if (!is_array($current) || !($current[0] instanceof Run)) {
                break;
            }

            restore_exception_handler();
        }
    }
}
