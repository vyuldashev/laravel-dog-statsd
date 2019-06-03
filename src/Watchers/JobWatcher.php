<?php

declare(strict_types=1);

namespace Vyuldashev\DogStatsD\Watchers;

use Graze\DogStatsD\Client;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Queue\Events\JobFailed;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Queue\Events\JobProcessing;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Str;

class JobWatcher
{
    protected $client;
    protected $events;

    protected $timers = [];

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
        Queue::createPayloadUsing(function ($connection, $queue, $payload) {
            $payload['job_watcher_uuid'] = (string)Str::uuid();
            return $payload;
        });

        $this->events->listen(JobProcessing::class, [$this, 'handleJobProcessingEvent']);
        $this->events->listen(JobProcessed::class, [$this, 'handleJobProcessedEvent']);
        $this->events->listen(JobFailed::class, [$this, 'handleJobFailedEvent']);
    }

    public function handleJobProcessingEvent(JobProcessing $event): void
    {
        $uuid = Arr::get($event->job->payload(), 'job_watcher_uuid');

        $this->timers[$uuid] = microtime(true);
    }

    public function handleJobProcessedEvent(JobProcessed $event): void
    {
        $uuid = Arr::get($event->job->payload(), 'job_watcher_uuid');

        if (!$uuid || !isset($this->timers[$uuid])) {
            return;
        }

        $timing = round((microtime(true) - $this->timers[$uuid]) * 1000, 4);

        $this->client->timing('_job_duration_seconds', $timing, [
            'connection' => $event->connectionName,
            'queue' => $event->job->getQueue(),
            'display_name' => $event->job->resolveName(),
        ]);

        unset($this->timers[$uuid]);
    }

    public function handleJobFailedEvent(JobFailed $event): void
    {
        $uuid = Arr::get($event->job->payload(), 'job_watcher_uuid');

        if (!$uuid || !isset($this->timers[$uuid])) {
            return;
        }

        $timing = round((microtime(true) - $this->timers[$uuid]) * 1000, 4);

        $this->client->timing('_job_duration_seconds', $timing, [
            'connection' => $event->connectionName,
            'queue' => $event->job->getQueue(),
            'display_name' => $event->job->resolveName(),
        ]);

        unset($this->timers[$uuid]);
    }
}
