<?php

declare(strict_types=1);

namespace Garanaw\LaravelConfigurer;

//use Garanaw\LaravelConfigurer\Dto\Options;
//use Garanaw\LaravelConfigurer\Enum\When;
//use Garanaw\LaravelConfigurer\Mechanisms\InstallMechanism;
//use Garanaw\LaravelConfigurer\Mechanisms\PublishMechanism;
//use Garanaw\LaravelConfigurer\Mechanisms\RequireMechanism;
//use Illuminate\Contracts\Console\Kernel;
//use Illuminate\Contracts\Events\Dispatcher;
//use Illuminate\Support\Composer;
//use Illuminate\Support\Enumerable;
//use Throwable;
//use function Laravel\Prompts\error;

class Installer //implements InstallerContract
{
//    protected array $libraries = [];

//    protected array $rejected = [];

//    protected array $completed = [];

//    public function __construct(
//        protected readonly Composer $composer,
//        protected readonly Dispatcher $events,
//        protected readonly Kernel $artisan,
//        protected readonly PublishMechanism $publisher,
//        protected readonly RequireMechanism $requirer,
//        protected readonly InstallMechanism $installer,
//    ) {}

//    public function run(Enumerable $libraries, Options $options): Enumerable
//    {
//        $this->requirer->install($libraries, $options);
//
//        // Lastly run all the commands that should be run after the entire installation process is done
//        foreach ($libraries as $library) {
//            try {
//                $this->installer->execute($library, When::END_ALL);
//            } catch (Throwable $e) {
//                error(sprintf('Failed to run after all install commands for %s: %s', $library->name, $e->getMessage()));
//
//                continue;
//            }
//
//            $this->completed[] = $library;
//        }
//
//        return collect($this->completed);
//    }

//    public function getFailed(): array
//    {
//        return array_merge(
//            $this->rejected,
//            $this->requirer->getFailed(),
//            $this->publisher->getFailed(),
//            $this->installer->getFailed()
//        );
//    }
}
