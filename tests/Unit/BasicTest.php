<?php

declare(strict_types=1);

namespace Tests\Unit;

use Tests\AbstractTestCase;

final class BasicTest extends AbstractTestCase
{
    /**
     * Test assert true is true.
     *
     * @return void
     */
    public function testAssertTrueIsTrue(): void
    {
        $this->assertTrue(true);
    }
}
