<?php

declare(strict_types=1);

namespace Garanaw\LaravelConfigurer\CustomInstallCommands\Concerns;

trait CanRun
{
    private bool $ran = false;

    public function setRan(bool $ran = true): static
    {
        $this->ran = $ran;

        return $this;
    }

    public function didRun(): bool
    {
        return $this->ran;
    }
}
