<?php

declare(strict_types=1);

namespace Garanaw\LaravelConfigurer\Mechanisms;

use Garanaw\LaravelConfigurer\Events\LibraryFailedRequiring;
use Garanaw\LaravelConfigurer\Events\LibraryRequired;
use Garanaw\LaravelConfigurer\Exception\FailedToRequireException;
use Garanaw\LaravelConfigurer\Library;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\Composer;
use Throwable;

use function Laravel\Prompts\confirm;

class RequireMechanism
{
    private array $failedToRequire = [];

    public function __construct(
        protected readonly Composer $composer,
        protected readonly Dispatcher $events,
    ) {}

    public function execute(Library $library): void
    {
        $command = $library->command;

        $dev = $library->canBeDevOnly && confirm("Do you want to install {$library->name} as a dev dependency?");

        $output = prompts_output(...);

        try {
            $installed = $this->composer->requirePackages([$command], $dev, $output);

            if (! $installed) {
                throw FailedToRequireException::fromLibrary($library);
            }
        } catch (Throwable $e) {
            $this->events->dispatch(new LibraryFailedRequiring($library, $e));

            $this->failedToRequire[$library->name] = [
                'library' => $library,
                'exception' => $e,
            ];

            throw $e;
        }

        $library->required();

        $this->events->dispatch(new LibraryRequired($library));
    }

    public function getFailed(): array
    {
        return $this->failedToRequire;
    }
}
