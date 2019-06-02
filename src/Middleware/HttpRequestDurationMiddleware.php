<?php

declare(strict_types=1);

namespace Vyuldashev\DogStatsD\Middleware;

use Closure;
use Graze\DogStatsD\Client;
use Illuminate\Http\Request;

class HttpRequestDurationMiddleware
{
    protected $client;
    protected $instance;

    protected $except = [
        //
    ];

    public function __construct(Client $client)
    {
        $this->client = $client ?? Client::instance($this->instance);
    }

    /**
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($this->inExceptArray($request)) {
            return $next($request);
        }

        $timerStart = microtime(true);

        $response = $next($request);

        $timerEnd = microtime(true);

        $uri = optional($request->route())->uri() ?? $request->getPathInfo();

        $this->client->timing(
            'http_request_duration_seconds',
            round(($timerEnd - $timerStart) * 1000, 4),
            [
                'scheme' => $request->getScheme(),
                'host' => $request->getHost(),
                'method' => $request->getMethod(),
                'uri' => $uri,
                'status_code' => $response->getStatusCode(),
            ]
        );

        return $response;
    }

    protected function inExceptArray($request): bool
    {
        foreach ($this->except as $except) {
            if ($except !== '/') {
                $except = trim($except, '/');
            }

            if ($request->fullUrlIs($except) || $request->is($except)) {
                return true;
            }
        }

        return false;
    }
}
