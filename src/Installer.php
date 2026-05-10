<?php

declare(strict_types=1);

namespace Garanaw\LaravelConfigurer;

use Garanaw\LaravelConfigurer\Contracts\InstallerContract;
use Garanaw\LaravelConfigurer\Dto\Options;
use Garanaw\LaravelConfigurer\Enum\When;
use Garanaw\LaravelConfigurer\Mechanisms\InstallMechanism;
use Garanaw\LaravelConfigurer\Mechanisms\PublishMechanism;
use Garanaw\LaravelConfigurer\Mechanisms\RequireMechanism;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\Composer;
use Illuminate\Support\Enumerable;
use Throwable;
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
        protected readonly PublishMechanism $publisher,
        protected readonly RequireMechanism $requirer,
        protected readonly InstallMechanism $installer,
    ) {}

    public function run(Enumerable $libraries, Options $options): Enumerable
    {
        $this->requirer->install($libraries, $options);

//        /** @var Library $library */
//        foreach ($libraries as $library) {
//            if (! $options->autoConfirm && ! confirm(sprintf('Do you still want to install %s?', $library->name))) {
//                $this->rejected[] = $library;
//
//                continue;
//            }
//
//            $this->events->dispatch(new LibraryStartedInstalling($library));
//
//            try {
//                // First run all the preparation commands for the given library if any
//                $this->installer->execute($library, When::START);
//            } catch (Throwable $e) {
//                error(sprintf('Failed to install commands before requiring %s: %s', $library->name, $e->getMessage()));
//
//                continue;
//            }
//
//            try {
//                // Require the library...
//                $this->requirer->execute($library);
//            } catch (Throwable $e) {
//                error(sprintf('Failed to require %s: %s', $library->name, $e->getMessage()));
//
//                continue;
//            }
//
//            try {
//                // Publish all the available assets
//                $this->publisher->execute($library);
//            } catch (Throwable $e) {
//                error(sprintf('Failed to publish %s: %s', $library->name, $e->getMessage()));
//
//                continue;
//            }
//
//            try {
//                // And run the installation commands that should be run right after the library is installed
//                $this->installer->execute($library, When::END);
//            } catch (Throwable $e) {
//                error(sprintf('Failed to run install commands for %s: %s', $library->name, $e->getMessage()));
//
//                continue;
//            }
//        }

        // Lastly run all the commands that should be run after the entire installation process is done
        foreach ($libraries as $library) {
            try {
                $this->installer->execute($library, When::END_ALL);
            } catch (Throwable $e) {
                error(sprintf('Failed to run after all install commands for %s: %s', $library->name, $e->getMessage()));

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
