<?php

/**
 * Omega Application - config/logging configuration file.
 *
 * @link      https://omegamvc.github.io
 * @author    Adriano Giovannini <agisoftt@gmail.com>
 * @copyright Copyright (c) 2024 - 2025 Adriano Giovannini (https://omegamvc.github.io)
 * @license   https://www.gnu.org/licenses/gpl-3.0-standalone.html     GPL V3.0+
 */

declare(strict_types=1);

use Omega\Logging\LogLevel;

use function Omega\Application\get_path;

/**
 * Return an array of configuration for logger.
 *
 * @category  Omega
 * @package   Config
 * @link      https://omegamvc.github.io
 * @author    Adriano Giovannini <agisoftt@gmail.com>
 * @copyright Copyright (c) 2024 - 2025 Adriano Giovannini (https://omegamvc.github.io)
 * @license   https://www.gnu.org/licenses/gpl-3.0-standalone.html     GPL V3.0+
 */
return [
    /*
     * Default Log Channel.
     *
     * This options defines the default log channel that gets used when writing
     * messages to the log. The name specified in this option  should match one
     * of the channels defined in the "channels" configuration array.
     */
    'default' => 'stream',
    'stream'  => [
        'type'    => 'stream',
        'path'    => get_path('path.logs') . 'omega.log',
        'minimum' => LogLevel::DEBUG,
        'options' => [
            /**
             * Specifies the file extension for the log files.
             * Default is 'txt'.
             */
            'extension' => 'txt',

            /**
             * Defines the format for the timestamp in log entries,
             * using PHP's date() function format. Default is
             * "Y-m-d G:i:s.u" (e.g., 2024-10-16 15:45:01.123456).
             */
            'dateFormat' => 'Y-m-d G:i:s.u',

            /**
             * The specific name of the log file. If set to false,
             * the logger will dynamically generate a filename,
             * typically based on the date or other criteria.
             */
            'filename' => false,

            /**
             * Determines how often the log buffer is flushed to disk.
             * For example, setting this to 100 would flush the logs
             * every 100 entries. false disables automatic flushing.
             */
            'flushFrequency' => false,

            /**
             * A string that is prefixed to the generated log file name,
             * such as 'log_' or 'error_'. This helps to distinguish
             * between different types of log files.
             */
            'prefix' => 'log_',

            /**
             * Allows customization of the log entry format. For example,
             * it might be something like [{date}] {level}: {message},
             * where placeholders are dynamically replaced with actual
             * log data. false indicates no custom format is applied.
             */
            'logFormat' => false,

            /**
             * A boolean flag indicating whether additional context
             * information (e.g., extra data related to the log entry)
             * should be appended to each log message. Default is true.
             */
            'appendContext' => true,
        ],
    ],
];
