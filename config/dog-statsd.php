<?php

use Illuminate\Support\Str;

return [

    'default' => env('DOG_STATSD_INSTANCE', 'server1'),

    'tags' => [
        'app_name' => Str::slug(env('APP_NAME', 'laravel'), '_'),
        'env' => env('APP_ENV', 'production'),
    ],

    'instances' => [

        'server1' => [
            'host' => env('DOG_STATSD_SERVER1_HOST', '127.0.0.1'),
            'port' => env('DOG_STATSD_SERVER1_PORT', 8125),
            'namespace' => 'laravel',
        ],

    ],

    // TODO
    'metrics' => [

    ],

];
