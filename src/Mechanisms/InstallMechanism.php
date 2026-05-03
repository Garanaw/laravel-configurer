<?php

declare(strict_types=1);

namespace Garanaw\LaravelConfigurer\Mechanisms;

use Garanaw\LaravelConfigurer\CustomInstallCommands\InstallCommand;
use Garanaw\LaravelConfigurer\Enum\When;
use Garanaw\LaravelConfigurer\Events\LibraryFailedInstalling;
use Garanaw\LaravelConfigurer\Events\LibraryInstalled;
use Garanaw\LaravelConfigurer\Exception\FailedToInstallException;
use Garanaw\LaravelConfigurer\Library;
use Illuminate\Contracts\Events\Dispatcher;
use Throwable;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\info;
use function Laravel\Prompts\warning;

class InstallMechanism
{
    private array $failedToInstall = [];

    public function __construct(
        protected readonly Dispatcher $events,
    ) {
    }

    public function execute(Library $library, When $when): void
    {
        if (!$library->hasInstallCommands()) {
            info("No installation commands for {$library->name}, skipping installation.");
            return;
        }

        $targetCommands = $library->installCommands->filter(
            static fn (InstallCommand $command) => $command->when()->is($when)
        );

        foreach ($targetCommands as $command) {
            if (!confirm("Do you want to run {$command->command()} now?")) {
                continue;
            }

            try {
                $command->install($library);
            } catch (Throwable $e) {
                $exception = FailedToInstallException::fromLibrary($library, $e);

                $this->failedToInstall[$library->name] = [
                    'library' => $library,
                    'exception' => $exception,
                ];

                $this->events->dispatch(new LibraryFailedInstalling($library, $exception));

                continue;
            }

            $command->setRan(true);
        }

        if (!isset($this->failedToInstall[$library->name])) {
            $library->installed();
        }

        $this->events->dispatch(new LibraryInstalled($library));
    }

    public function getFailed(): array
    {
        return $this->failedToInstall;
    }
}
