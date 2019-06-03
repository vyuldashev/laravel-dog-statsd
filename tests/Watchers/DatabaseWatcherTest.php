<?php

declare(strict_types=1);

namespace Vyuldashev\DogStatsD\Tests\Watchers;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Vyuldashev\DogStatsD\Tests\TestCase;
use Vyuldashev\DogStatsD\Tests\User;

class DatabaseWatcherTest extends TestCase
{
    public function test(): void
    {
        $this->markTestIncomplete();

        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('username');
            $table->timestamps();
        });

        User::create(['username' => 'foo']);

        $this->assertSame(1, User::count());
    }

    protected function getEnvironmentSetUp($app): void
    {
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);

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
