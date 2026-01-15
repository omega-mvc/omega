<?php

declare(strict_types=1);

use Omega\Database\Schema\Table\Create;
use Omega\Support\Facades\Schema;

return [
    'up' => [
        Schema::table('cron', function (Create $column) {
            $column('id')->int(4)->autoIncrement(true);
            $column('message')->varChar(300);
            $column('context')->varChar(1_000);
            $column('date_create')->int(15);

            $column->primaryKey('id');
        }),
    ],
    'down' => [
        Schema::drop()->table('cron'),
    ],
];
