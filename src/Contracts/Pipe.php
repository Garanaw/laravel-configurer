<?php

declare(strict_types=1);

namespace Garanaw\LaravelConfigurer\Contracts;

use Garanaw\LaravelConfigurer\Dto\Passable;

interface Pipe
{
    public function handle(Passable $passable, \Closure $next): Passable;
}