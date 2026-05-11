<?php

/**
 * Omega Application - config/filesystem configuration file.
 *
 * @link      https://omegamvc.github.io
 * @author    Adriano Giovannini <agisoftt@gmail.com>
 * @copyright Copyright (c) 2024 - 2025 Adriano Giovannini (https://omegamvc.github.io)
 * @license   https://www.gnu.org/licenses/gpl-3.0-standalone.html     GPL V3.0+
 */

declare(strict_types=1);

use function Omega\Environment\env;
use function Omega\Application\get_path;

/**
 * Return an array of filesystem configuration parameters.
 *
 * @category  Omega
 * @package   Config
 * @link      https://omegamvc.github.io
 * @author    Adriano Giovannini <agisoftt@gmail.com>
 * @copyright Copyright (c) 2024 - 2025 Adriano Giovannini (https://omegamvc.github.io)
 * @license   https://www.gnu.org/licenses/gpl-3.0-standalone.html     GPL V3.0+
 */
return [
    /**
     * Default Filesystem Disk.
     *
     * Here you may specify the default filesystem disk that should be used
     * by the framework. The "Local" disk, as  a wel as a  variety of cloud
     * based disks are available to your application.
     */
    'default' => env('FILESYSTEM_DISK', 'local'),

    /**
     * Filesystem Disk.
     *
     * Here you may configure as many filesystem "disks" as you which, and
     * you may even configure multiple disks  of the sam driver.  Defaults
     * have  been  set up for  each driver  as an example  of the required
     * values.
     *
     * Supported driver:
     *
     * `local`
     * `s3`
     * `s3async`
     * `ftp`
     */
    'local' => [
        'type' => 'local',
        'path' => get_path('path.app'),
    ],
    's3' => [
        'type'    => 's3',
        'key'     => env('AWS_ACCESS_KEY_ID'),
        'secret'  => env('AWS_SECRET_ACCESS_KEY'),
        'token'   => env('AWS_TOKEN'),
        'region'  => env('AWS_DEFAULT_REGION'),
        'bucket'  => env('AWS_BUCKET'),
        'options' => [
            'create' => true,
        ],
    ],
    'asyncs3' => [
        'type'    => 'asyncs3',
        'key'     => env('AWS_ACCESS_KEY_ID'),
        'secret'  => env('AWS_SECRET_ACCESS_KEY'),
        'token'   => env('AWS_TOKEN'),
        'region'  => env('AWS_DEFAULT_REGION'),
        'bucket'  => env('AWS_BUCKET'),
        'options' => [
            'create' => true,
        ],
    ],
    'ftp' => [
        'type'     => 'ftp',
        'host'     => env('FTP_HOST'),
        'username' => env('FTP_USERNAME'),
        'password' => env('FTP_PASSWORD'),
        'passive'  => env('FTP_PASSIVE'),
        'create'   => env('FTP_CREATE'),
        'mode'     => env('FTP_MODE'),
        'ssl'      => env('FTP_SSL'),
        'timeout'  => env('FTP_TIMEOUT'),
        'utf8'     => env('FTP_UTF8'),
    ],
];
