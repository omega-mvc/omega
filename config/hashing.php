<?php

declare(strict_types=1);

use function Omega\Support\env;

return [
    'bcrypt'   => env('BCRYPT_ROUNDS', 12),
];
