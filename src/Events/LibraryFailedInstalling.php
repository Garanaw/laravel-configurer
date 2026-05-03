<?php

declare(strict_types=1);

namespace Garanaw\LaravelConfigurer\Events;

use Garanaw\LaravelConfigurer\Library;
use Throwable;

class LibraryFailedInstalling
{
    public function __construct(public Library $library, public Throwable $exception) {}
}