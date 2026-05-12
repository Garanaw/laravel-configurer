<?php

declare(strict_types=1);

namespace Garanaw\LaravelConfigurer\Pipeline;

use Garanaw\LaravelConfigurer\Dto\Passable;
use Garanaw\LaravelConfigurer\Enum\When;
use Illuminate\Pipeline\Pipeline;

class ComposerPipeline extends Pipeline
{
    public function pass(Passable $passable): mixed
    {
        return $this
            ->send($passable)
            ->through($this->filterPipes($passable))
            ->thenReturn();
    }

    protected function filterPipes(Passable $passable): array
    {
        $pipes = [
            ['class' => Pipes\RequirerPipe::class],
        ];

        if ($passable->hasDevLibraries()) {
            $pipes[] = ['class' => Pipes\DevRequirerPipe::class];
        }

        if ($passable->shouldPublish()) {
            $pipes[] = ['class' => Pipes\PublisherPipe::class];
        }

        if ($passable->shouldInstall()) {
            $pipes[] = ['class' => Pipes\InstallerPipe::class, 'params' => ['when' => When::END]];
        }

        if ($passable->shouldMigrate()) {
            $pipes[] = ['class' => Pipes\MigratorPipe::class];
        }

        if ($passable->shouldInstall()) {
            $pipes[] = ['class' => Pipes\InstallerPipe::class, 'params' => ['when' => When::END_ALL]];
        }

        $pipes[] = ['class' => Pipes\CustomCommandsPipe::class];

        if ($customPipes = config('configurer.customPipes')) {
            $pipes = array_merge($pipes, $customPipes);
        }

        return array_map(
            static function (array $class) {
                try {
                    return resolve($class['class'], $class['params'] ?? []);
                } catch (\Throwable $e) {
                    dd([
                        'class' => $class['class'],
                        'params' => $class['params'] ?? [],
                        'error' => $e->getMessage(),
                    ]);
                }
            },
            $pipes,
        );
    }
}