<?php

declare(strict_types=1);

namespace Garanaw\LaravelConfigurer\Mechanisms;

use Garanaw\LaravelConfigurer\Dto\Options;
use Garanaw\LaravelConfigurer\Events\LibraryFailedRequiring;
use Garanaw\LaravelConfigurer\Events\LibraryRequired;
use Garanaw\LaravelConfigurer\Exception\FailedToRequireException;
use Garanaw\LaravelConfigurer\Library;
use Illuminate\Console\OutputStyle;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\Composer;
use Illuminate\Support\Enumerable;
use Throwable;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\error;

class RequireMechanism
{
    private array $failedToRequire = [];

    public function __construct(
        protected readonly Composer $composer,
        protected readonly Dispatcher $events,
        protected readonly OutputStyle $output,
    ) {}

    public function install(Enumerable $libraries, Options $options): void
    {
        $commands = $this->buildCommands($libraries, $options);

        try {
            $installed = $this->composer->requirePackages(
                packages: $commands['toInstall'],
                output: $this->output,
            );
        } catch (Throwable $e) {
            error(sprintf('Failed to require packages: %s', $e->getMessage()));
        }

        if (! empty($commands['devToInstall'])) {
            try {
                $installedDev = $this->composer->requirePackages(
                    packages: $commands['devToInstall'],
                    dev: true,
                    output: $this->output,
                );
            } catch (Throwable $e) {
                error(sprintf('Failed to require dev packages: %s', $e->getMessage()));
            }
        }
    }

    protected function buildCommands(Enumerable $libraries, Options $options): array
    {
        /**
         * @var Enumerable<Library> $dev
         * @var Enumerable<Library> $noDev
         */
        [$dev, $noDev] = $libraries->partition(static fn (Library $library) => $library->canBeDevOnly);

        $commands = [
            'toInstall' => $noDev->map(static fn (Library $library) => $library->command),
            'devToInstall' => [],
        ];

        if ($options->devOnly) {
            $commands['devToInstall'] = $dev->map(static fn (Library $library) => $library->command);
        } else {
            foreach ($dev as $library) {
                if (confirm(sprintf('Do you want to install %s as a dev dependency?', $library->name))) {
                    $commands['devToInstall'][] = $library->command;
                } else {
                    $commands['toInstall'][] = $library->command;
                }
            }
        }

        return [
            'toInstall' => $commands['toInstall']->all(),
            'devToInstall' => $commands['devToInstall']->all(),
        ];
    }

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
