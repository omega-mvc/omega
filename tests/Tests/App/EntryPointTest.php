<?php

declare(strict_types=1);

namespace Tests\App;

use PHPUnit\Framework\TestCase;

class EntryPointTest extends TestCase
{
    public function testKeepsTheReasonAKernelCouldNotBeBuilt(): void
    {
        // The entry point cannot be exercised in process: it requires bootstrap/app.php
        // with require_once, so a suite that has already booted the application would get
        // `true` back instead of the application and would not be testing this file at all.
        // It is run as a child process instead, with a kernel that cannot be built.
        $prepend = sys_get_temp_dir() . '/omega-entry-' . bin2hex(random_bytes(6)) . '.php';

        file_put_contents(
            $prepend,
            <<<'PHP'
<?php

declare(strict_types=1);

namespace App\Kernel;

use ReflectionException;

class HttpKernel
{
    /**
     * @param object $app Ignored: the constructor only exists to fail.
     */
    public function __construct(object $app)
    {
        throw new ReflectionException('the http kernel cannot be built');
    }
}
PHP
        );

        $root = dirname(__DIR__, 3);

        $command = sprintf(
            '%s -d xdebug.mode=off -d auto_prepend_file=%s %s',
            escapeshellarg(PHP_BINARY),
            escapeshellarg($prepend),
            escapeshellarg($root . '/public/index.php')
        );

        $output = (string) shell_exec($command . ' 2>&1');

        unlink($prepend);

        // The reason has to survive: it is the only thing that says what to fix.
        $this->assertStringContainsString('the http kernel cannot be built', $output);

        // And the symptom of losing it must not be there instead.
        $this->assertStringNotContainsString('on null', $output);
    }
}
