<?php

declare(strict_types=1);

namespace Vyuldashev\DogStatsD\Tests\Watchers;

use Illuminate\Support\Facades\Route;
use Vyuldashev\DogStatsD\Tests\TestCase;

class RequestWatcherTest extends TestCase
{
    public function test(): void
    {
        Route::get('/users', function () {
            return 'Hello';
        });

        Route::post('/users', function () {
            return response(201);
        });

        $this->get('/users');
        $this->post('/users');
    }

    protected function getEnvironmentSetUp($app): void
    {
        $app['config']->set('dog-statsd.default', 'statsd_exporter');

        $app['config']->set('dog-statsd.instances.statsd_exporter', [
            'host' => '127.0.0.1',
            'port' => 9125,
            'namespace' => 'laravel_dog_statsd',
            'tags' => [
                'host' => 'localhost',
            ],
        ]);
    }
}
