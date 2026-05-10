<?php

declare(strict_types=1);

namespace Garanaw\LaravelConfigurer\Contracts;

use Garanaw\LaravelConfigurer\Dto\Options;
use Illuminate\Support\Enumerable;

interface InstallerContract
{
    public function run(Enumerable $libraries, Options $options): Enumerable;
}
