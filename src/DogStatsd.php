<?php

namespace Vyuldashev\DogStatsD;

use Graze\DogStatsD\Client;
use Illuminate\Support\Facades\Facade;

class DogStatsd extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return Client::class;
    }
}
