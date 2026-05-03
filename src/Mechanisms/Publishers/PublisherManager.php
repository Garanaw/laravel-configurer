<?php

declare(strict_types=1);

namespace Garanaw\LaravelConfigurer\Mechanisms\Publishers;

use Garanaw\LaravelConfigurer\Library;
use Illuminate\Support\Manager;

class PublisherManager extends Manager
{
    protected Library $library;

    public function driver($driver = null): PublisherContract
    {
        if (! $driver instanceof Library) {
            throw new \InvalidArgumentException('Driver must be an instance of ' . Library::class);
        }

        $this->library = $driver;

        $options = $driver->publishCommands;

        if (isset($options['commands'])) {
            return parent::driver('command');
        }

        if (isset($options['provider'])) {
            return parent::driver('provider');
        }

        return parent::driver($this->getDefaultDriver());
    }

    public function getDefaultDriver()
    {
        return 'null';
    }

    protected function createCommandDriver(): PublisherContract
    {
        return $this->container->make(CommandPublisher::class);
    }

    protected function createProviderDriver(): PublisherContract
    {
        return $this->container->make(ProviderPublisher::class);
    }

    protected function createNullDriver(): PublisherContract
    {
        return $this->container->make(NullDriver::class);
    }
}
