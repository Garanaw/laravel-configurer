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
 */
class Options extends Fluent
{

}