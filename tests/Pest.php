<?php

declare(strict_types=1);

use Tests\AbstractTestCase;

$_ENV['APP_ENV'] = 'testing';

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "pest()" function to bind different classes or traits.
|
*/

pest()->extend(AbstractTestCase::class)->in('Feature', 'Unit');
