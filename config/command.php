<?php

declare(strict_types=1);

use Omega\Console\Commands\ClearCacheCommand;
use Omega\Console\Commands\ConfigCommand;
use Omega\Console\Commands\CronCommand;
use Omega\Console\Commands\HelpCommand;
use Omega\Console\Commands\MaintenanceCommand;
use Omega\Console\Commands\MakeCommand;
use Omega\Console\Commands\MigrationCommand;
use Omega\Console\Commands\PackageDiscoveryCommand;
use Omega\Console\Commands\RouteCacheCommand;
use Omega\Console\Commands\RouteCommand;
use Omega\Console\Commands\SeedCommand;
use Omega\Console\Commands\ServeCommand;
use Omega\Console\Commands\VendorImportCommand;
use Omega\Console\Commands\ViewCommand;

return [
    'commands' => [
        HelpCommand::$command,
        CronCommand::$command,
        MakeCommand::$command,
        ServeCommand::$command,
        RouteCommand::$command,
        RouteCacheCommand::$command,
        MigrationCommand::$command,
        SeedCommand::$command,
        ViewCommand::$command,
        MaintenanceCommand::$command,
        ConfigCommand::$command,
        PackageDiscoveryCommand::$command,
        ClearCacheCommand::$command,
        VendorImportCommand::$command,
    ],
];
