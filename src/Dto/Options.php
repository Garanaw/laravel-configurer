<?php

declare(strict_types=1);

namespace Garanaw\LaravelConfigurer\Dto;

use Illuminate\Support\Fluent;

/**
 * @property-read bool $all
 * @property-read bool $autoConfirm
 * @property-read bool $devAsDev
 * @property-read bool $devOnly
 * @property-read bool $noPublish
 * @property-read bool $noInstall
 * @property-read bool $noMigrate
 * @property-read bool $noEvents
 * @property-read bool $noEnv
 * @property-read bool $verbose
 */
class Options extends Fluent
{
    public function shouldInstallAll(): bool
    {
        return $this->all;
    }

    public function shouldAutoConfirm(): bool
    {
        return $this->autoConfirm;
    }

    public function shouldInstallDevAsDev(): bool
    {
        return $this->devAsDev;
    }

    public function shouldInstallOnlyDev(): bool
    {
        return $this->devOnly;
    }

    public function shouldPublish(): bool
    {
        return ! $this->noPublish;
    }

    public function shouldInstall(): bool
    {
        return ! $this->noInstall;
    }

    public function shouldMigrate(): bool
    {
        return ! $this->noMigrate;
    }

    public function shouldDispatchEvents(): bool
    {
        return ! $this->noEvents;
    }

    public function shouldAddEnvVars(): bool
    {
        return ! $this->noEnv;
    }

    public function isVerbose(): bool
    {
        return $this->verbose;
    }
}