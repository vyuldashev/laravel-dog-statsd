<?php

namespace Vyuldashev\DogStatsD\Tests;

use Graze\DogStatsD\Client;

class ClientTest extends TestCase
{
    public function testConfiguration(): void
    {
        /** @var Client $client */
        $client = $this->app[Client::class];

        $this->assertSame('127.0.0.1', $client->getHost());
        $this->assertSame(9125, $client->getPort());
        $this->assertSame('laravel_dog_statsd', $client->getNamespace());

        $server1 = Client::instance('server1');

        $this->assertSame('127.0.0.1', $server1->getHost());
        $this->assertSame(8125, $server1->getPort());
        $this->assertSame('laravel', $server1->getNamespace());

        $statsdExporter = Client::instance('statsd_exporter');

        $this->assertSame('127.0.0.1', $statsdExporter->getHost());
        $this->assertSame(9125, $statsdExporter->getPort());
        $this->assertSame('laravel_dog_statsd', $statsdExporter->getNamespace());
    }

    public function testWrite(): void
    {
        /** @var Client $statsdExporterClient */
        $statsdExporterClient = $this->app[Client::class];

        $statsdExporterClient->increment('counter');

        $this->assertSame('laravel_dog_statsd.counter:1|c|#env:testing,host:localhost', $statsdExporterClient->getLastMessage());
        $this->assertTrue($statsdExporterClient->wasSuccessful());

        $server1 = Client::instance('server1');
        $server1->increment('counter1');

        $this->assertSame('laravel.counter1:1|c|#env:testing', $server1->getLastMessage());
        $this->assertTrue($server1->wasSuccessful());
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
