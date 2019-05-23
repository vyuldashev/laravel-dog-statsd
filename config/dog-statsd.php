<?php

use Illuminate\Support\Str;

return [

    'default' => env('DOG_STATSD_INSTANCE', 'server1'),

    'tags' => [
        'env' => app()->environment(),
    ],

    'instances' => [

        'server1' => [
            'host' => env('DOG_STATSD_SERVER1_HOST', '127.0.0.1'),
            'port' => env('DOG_STATSD_SERVER1_PORT', 8125),
            'namespace' => env('DOG_STATSD_SERVER1_NAMESPACE', Str::slug(env('APP_NAME', 'laravel'), '_')),
        ],

    ],

];
