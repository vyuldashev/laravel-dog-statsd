<?php

declare(strict_types=1);

namespace Vyuldashev\DogStatsD\Watchers;

use Graze\DogStatsD\Client;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Foundation\Http\Events\RequestHandled;
use Illuminate\Http\Request;

class RequestWatcher
{
    protected $client;
    protected $events;

    protected $except = [
        //
    ];

    public function __construct(
        Client $client,
        Dispatcher $events
    )
    {
        $this->client = $client;
        $this->events = $events;
    }

    public function register(): void
    {
        $this->events->listen(RequestHandled::class, [$this, 'handleRequestHandledEvent']);
    }

    public function handleRequestHandledEvent(RequestHandled $event): void
    {
        if (!defined('LARAVEL_START')) {
            return;
        }

        if ($this->inExceptArray($event->request)) {
            return;
        }

        $uri = optional($event->request->route())->uri() ?? $event->request->getPathInfo();

        $this->client->timing(
            'http_request_duration_seconds',
            round((microtime(true) - LARAVEL_START) * 1000, 4),
            [
                'scheme' => $event->request->getScheme(),
                'host' => $event->request->getHost(),
                'method' => $event->request->getMethod(),
                'uri' => $uri,
                'status_code' => $event->response->getStatusCode(),
            ]
        );
    }

    /**
     * @param Request $request
     * @return bool
     */
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
