<?php

declare(strict_types=1);

use function Omega\Support\env;

return [
    'columns' => env('TERMINAL_COLUMNS', '')
];
