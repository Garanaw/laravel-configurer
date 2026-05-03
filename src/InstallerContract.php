<?php

declare(strict_types=1);

namespace Garanaw\LaravelConfigurer;

use Illuminate\Support\Enumerable;

interface InstallerContract
{
    public function run(Enumerable $libraries): Enumerable;
}
