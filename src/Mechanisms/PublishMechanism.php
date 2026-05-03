<?php

declare(strict_types=1);

namespace Garanaw\LaravelConfigurer\Mechanisms;

use Garanaw\LaravelConfigurer\Events\LibraryFailedPublishing;
use Garanaw\LaravelConfigurer\Events\LibraryPublished;
use Garanaw\LaravelConfigurer\Exception\FailedToPublishException;
use Garanaw\LaravelConfigurer\Library;
use Garanaw\LaravelConfigurer\Mechanisms\Publishers\PublisherManager;
use Illuminate\Contracts\Events\Dispatcher;
use Throwable;

use function Laravel\Prompts\confirm;

class PublishMechanism
{
    protected array $failedToPublish = [];

    public function __construct(
        protected readonly PublisherManager $publisher,
        protected readonly Dispatcher $events,
    ) {
    }

    public function execute(Library $library): void
    {
        if (! $library->hasPublishCommands()) {
            return;
        }

        $shouldPublish = confirm("Do you want to publish the assets for {$library->name} now?");

        if (! $shouldPublish) {
            return;
        }

        $driver = $this->publisher->driver($library);

        try {
            $driver->publish($library);
        } catch (Throwable $e) {
            $exception = FailedToPublishException::fromLibrary($library, $e);

            $this->events->dispatch(new LibraryFailedPublishing($library, $exception));

            $this->failedToPublish[$library->name] = [
                'library' => $library,
                'exception' => $exception,
            ];

            throw $exception;
        }

        $library->published();

        $this->events->dispatch(new LibraryPublished($library));
    }

    public function getFailed(): array
    {
        return $this->failedToPublish;
    }
}
