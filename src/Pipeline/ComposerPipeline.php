<?php

declare(strict_types=1);

namespace Garanaw\LaravelConfigurer\Pipeline;

use Garanaw\LaravelConfigurer\Dto\Passable;
use Illuminate\Pipeline\Pipeline;

use function Laravel\Prompts\table;

class ComposerPipeline extends Pipeline
{
    public function pass(Passable $passable): Passable
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
            $pipes[] = ['class' => Pipes\InstallerPipe::class];
        }

//        if ($passable->shouldMigrate()) {
//            $pipes[] = ['class' => Pipes\MigratorPipe::class];
//        }
//
//        if ($passable->shouldInstall()) {
//            $pipes[] = ['class' => Pipes\InstallerPipe::class];
//        }
//
//        $pipes[] = ['class' => Pipes\CustomCommandsPipe::class];

        if ($customPipes = config('configurer.customPipes')) {
            $pipes = array_merge($pipes, $customPipes);
        }

        if ($passable->options->isVerbose()) {
            $this->display($pipes);
        }

        return array_map(
            static fn (array $class) => resolve($class['class'], $class['params'] ?? []),
            $pipes,
        );
    }

    private function display(array $pipes): void
    {
        $classes = array_map(static fn (array $pipe) => class_basename($pipe['class']), $pipes);

        table(
            headers: ['Pipes'],
            rows: $classes,
        );
    }
}