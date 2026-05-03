<?php

declare(strict_types=1);

namespace Garanaw\LaravelConfigurer\Mechanisms\Publishers;

use Garanaw\LaravelConfigurer\Library;

interface PublisherContract
{
    public function publish(Library $library): void;
}