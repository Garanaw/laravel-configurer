<?php

declare(strict_types=1);

namespace Garanaw\LaravelConfigurer\Mechanisms\Publishers;

use Garanaw\LaravelConfigurer\Library;

class NullDriver implements PublisherContract
{
    public function publish(Library $library): void
    {
        // No action taken
    }
}