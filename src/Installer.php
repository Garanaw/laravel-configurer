<?php

declare(strict_types=1);

namespace Garanaw\LaravelConfigurer;

use Garanaw\LaravelConfigurer\Enum\When;
use Garanaw\LaravelConfigurer\Events\LibraryStartedInstalling;
use Garanaw\LaravelConfigurer\Mechanisms\InstallMechanism;
use Garanaw\LaravelConfigurer\Mechanisms\PublishMechanism;
use Garanaw\LaravelConfigurer\Mechanisms\RequireMechanism;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\Composer;
use Illuminate\Support\Enumerable;
use Throwable;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\error;

class Installer implements InstallerContract
{
    protected array $libraries = [];

    protected array $rejected = [];

    protected array $completed = [];

    public function __construct(
        protected readonly Composer $composer,
        protected readonly Dispatcher $events,
        protected readonly Kernel $artisan,
        protected readonly RequireMechanism $requirer,
        protected readonly PublishMechanism $publisher,
        protected readonly InstallMechanism $installer,
    ) {}

    public function run(Enumerable $libraries): Enumerable
    {
        /** @var Library $library */
        foreach ($libraries as $library) {
            if (! confirm("Do you still want to install {$library->name}?")) {
                $this->rejected[] = $library;

                continue;
            }

            $this->events->dispatch(new LibraryStartedInstalling($library));

            try {
                // First run all the preparation commands for the given library if any
                $this->installer->execute($library, When::START);
            } catch (Throwable $e) {
                error("Failed to install commands before requiring {$library->name}: {$e->getMessage()}");

                continue;
            }

            try {
                // Require the library...
                $this->requirer->execute($library);
            } catch (Throwable $e) {
                error("Failed to require {$library->name}: {$e->getMessage()}");

                continue;
            }

            try {
                // Publish all the available assets
                $this->publisher->execute($library);
            } catch (Throwable $e) {
                error("Failed to publish {$library->name}: {$e->getMessage()}");

                continue;
            }

            try {
                // And run the installation commands that should be run right after the library is installed
                $this->installer->execute($library, When::END);
            } catch (Throwable $e) {
                error("Failed to install {$library->name}: {$e->getMessage()}");

                continue;
            }
        }

        // Lastly run all the commands that should be run after the entire installation process is done
        foreach ($this->libraries as $library) {
            try {
                $this->installer->execute($library, When::END_ALL);
            } catch (Throwable $e) {
                error("Failed to run after all install commands for {$library->name}: {$e->getMessage()}");

                continue;
            }

            $this->completed[] = $library;
        }

        return collect($this->completed);
    }

    public function getFailed(): array
    {
        return array_merge(
            $this->rejected,
            $this->requirer->getFailed(),
            $this->publisher->getFailed(),
            $this->installer->getFailed()
        );
    }
}
