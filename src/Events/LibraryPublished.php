<?php

declare(strict_types=1);

namespace Garanaw\LaravelConfigurer\Events;

use Garanaw\LaravelConfigurer\Library;

class LibraryPublished
{
    public function __construct(public Library $library) {}
}