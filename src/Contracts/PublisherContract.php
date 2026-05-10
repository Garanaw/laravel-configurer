<?php

declare(strict_types=1);

namespace Garanaw\LaravelConfigurer\Contracts;

use Garanaw\LaravelConfigurer\Library;

interface PublisherContract
{
    public function publish(Library $library): void;
}
