<?php

namespace Vyuldashev\DogStatsD\Tests;

use Orchestra\Testbench\TestCase as BaseTestCase;
use Vyuldashev\DogStatsD\DogStatsDServiceProvider;

class TestCase extends BaseTestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            DogStatsDServiceProvider::class,
        ];
    }
}
