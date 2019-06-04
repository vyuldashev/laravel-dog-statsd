<?php

declare(strict_types=1);

namespace Vyuldashev\DogStatsD;

use Graze\DogStatsD\Client;
use Illuminate\Support\Arr;
use Illuminate\Support\ServiceProvider;

class DogStatsDServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->publishes([
            __DIR__ . '/../config/dog-statsd.php' => config_path('dog-statsd.php'),
        ], 'dog-statsd-config');

        $this->mergeConfigFrom(
            __DIR__ . '/../config/dog-statsd.php', 'dog-statsd'
        );
    }

    public function boot(): void
    {
        $this->app->bind(Client::class, function ($app) {
            $config = Arr::get($app['config'], 'dog-statsd');

            $instances = Arr::get($config, 'instances', []);
            $defaultInstance = Arr::get($instances, $config['default'], []);

            if ($defaultTags = $this->resolveTagsForInstance($config, $config['default'])) {
                $defaultInstance['tags'] = $defaultTags;
            }

            $client = new Client();
            $client->configure($defaultInstance);

            foreach ($instances as $name => $instanceConfig) {
                $tags = $this->resolveTagsForInstance($config, $name);

                $instanceConfig['tags'] = $tags;

                Client::instance($name)->configure($instanceConfig);
            }

            return $client;
        });

        foreach (config('dog-statsd.watchers', []) as $watcher) {
            resolve($watcher)->register();
        }
    }

    private function resolveTagsForInstance(array $config, string $instance): array
    {
        $defaultTags = Arr::get($config, 'tags', []);
        $instanceTags = Arr::get($config, 'instances.' . $instance . '.tags', []);

        return array_merge($defaultTags, $instanceTags);
    }

    public function provides(): array
    {
        return [
            Client::class,
        ];
    }
}
