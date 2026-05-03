<?php

declare(strict_types=1);

namespace Garanaw\LaravelConfigurer\Events;

use Garanaw\LaravelConfigurer\Library;

class LibraryFailedPublishing
{
    public function __construct(public Library $library, public \Throwable $exception) {}
}