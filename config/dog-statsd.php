<?php

use Illuminate\Support\Str;
use Vyuldashev\DogStatsD\Watchers\JobWatcher;
use Vyuldashev\DogStatsD\Watchers\RequestWatcher;

return [

    'default' => env('DOG_STATSD_INSTANCE', 'server1'),

    'tags' => [
        'app_name' => Str::slug(env('APP_NAME', 'laravel'), '_'),
        'env' => env('APP_ENV', 'production'),
    ],

    'instances' => [

        'server1' => [
            'host' => env('DOG_STATSD_SERVER1_HOST', '127.0.0.1'),
            'port' => env('DOG_STATSD_SERVER1_PORT', 9125),
            'namespace' => 'laravel',
        ],

    ],

    'watchers' => [
        JobWatcher::class,
        RequestWatcher::class,
    ],

];
