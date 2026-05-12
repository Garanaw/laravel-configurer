<?php

declare(strict_types=1);

namespace Garanaw\LaravelConfigurer\Pipeline\Pipes;

use Garanaw\LaravelConfigurer\Contracts\Pipe;
use Garanaw\LaravelConfigurer\Dto\Passable;

class CustomCommandsPipe implements Pipe
{
    public function handle(Passable $passable, \Closure $next): Passable
    {
        return $next($passable);
    }
}