<?php

declare(strict_types=1);

namespace Garanaw\LaravelConfigurer\CustomInstallCommands\Concerns;

trait HasCustomId
{
    public function id(): string
    {
        return static::makeIdForDep();
    }

    public static function makeIdForDep(): string
    {
        return sprintf(
            'custom:%s',
            str(self::class)->replace('\\', '.')->toString()
        );
    }
}