<?php

declare(strict_types=1);

namespace Vyuldashev\DogStatsD\Tests\Watchers;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Vyuldashev\DogStatsD\Tests\TestCase;

class JobWatcherTest extends TestCase
{
    public function test(): void
    {
        ExampleJob::dispatch();
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

class ExampleJob implements ShouldQueue
{
    public $queue = 'default';

    use Dispatchable;

    public function handle(): void
    {
        sleep(1);
    }
}
