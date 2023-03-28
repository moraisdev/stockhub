<?php

use Monolog\Handler\NullHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\SyslogUdpHandler;

return [

    /*
    |--------------------------------------------------------------------------
    | Default Log Channel
    |--------------------------------------------------------------------------
    |
    | This option defines the default log channel that gets used when writing
    | messages to the logs. The name specified in this option should match
    | one of the channels defined in the "channels" configuration array.
    |
    */

    'default' => env('LOG_CHANNEL', 'stack'),

    /*
    |--------------------------------------------------------------------------
    | Log Channels
    |--------------------------------------------------------------------------
    |
    | Here you may configure the log channels for your application. Out of
    | the box, Laravel uses the Monolog PHP logging library. This gives
    | you a variety of powerful log handlers / formatters to utilize.
    |
    | Available Drivers: "single", "daily", "slack", "syslog",
    |                    "errorlog", "monolog",
    |                    "custom", "stack"
    |
    */

    'channels' => [
        'stack' => [
            'driver' => 'stack',
            'channels' => ['single', 'slack'],
            'ignore_exceptions' => false,
        ],

        'dev' => [
            'driver' => 'stack',
            'channels' => ['single', 'slack_dev'],
            'ignore_exceptions' => false,
        ],

        
        'dev_2' => [
            'driver' => 'stack',
            'channels' => ['single', 'slack_dev_2'],
            'ignore_exceptions' => false,
        ],

        'single' => [
            'driver' => 'single',
            'path' => storage_path('logs/laravel.log'),
            'level' => 'debug',
        ],

        'daily' => [
            'driver' => 'daily',
            'path' => storage_path('logs/laravel.log'),
            'level' => 'debug',
            'days' => 14,
        ],

        'slack' => [
            'driver' => 'slack',
            //'url' => 'https://hooks.slack.com/services/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',
            'url' => 'https://hooks.slack.com/services/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', //webhook novo oficial
            'username' => 'Laravel Log Oficial',
            'emoji' => ':boom:',
            'level' => 'error',
        ],

        'slack_dev' => [
            'driver' => 'slack',
            'url' => 'https://hooks.slack.com/services/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',
            'username' => 'Laravel Log Dev',
            'emoji' => ':boom:',
            'level' => 'error',
        ],

        'slack_dev_2' => [
            'driver' => 'slack',
            'url' => 'https://hooks.slack.com/services/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',
            'username' => 'Laravel Log Dev 2',
            'emoji' => ':boom:',
            'level' => 'error',
        ],

        'papertrail' => [
            'driver' => 'monolog',
            'level' => 'debug',
            'handler' => SyslogUdpHandler::class,
            'handler_with' => [
                'host' => env('PAPERTRAIL_URL'),
                'port' => env('PAPERTRAIL_PORT'),
            ],
        ],

        'stderr' => [
            'driver' => 'monolog',
            'handler' => StreamHandler::class,
            'formatter' => env('LOG_STDERR_FORMATTER'),
            'with' => [
                'stream' => 'php://stderr',
            ],
        ],

        'syslog' => [
            'driver' => 'syslog',
            'level' => 'debug',
        ],

        'errorlog' => [
            'driver' => 'errorlog',
            'level' => 'debug',
        ],

        'null' => [
            'driver' => 'monolog',
            'handler' => NullHandler::class,
        ],

        'emergency' => [
            'path' => storage_path('logs/laravel.log'),
        ],
    ],

];
