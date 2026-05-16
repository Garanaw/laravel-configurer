<?php

declare(strict_types=1);

namespace Garanaw\LaravelConfigurer\Pipeline;

use Garanaw\LaravelConfigurer\Dto\Passable;
use Garanaw\LaravelConfigurer\Enum\When;
use Illuminate\Pipeline\Pipeline;

use function Laravel\Prompts\info;

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
        array_map(static fn(array $pipe) => class_basename($pipe['class']), $pipes)
            |> (static fn($x) => implode(', ', $x))
            |> (static fn($x) => sprintf('The following pipes will be executed: %s', $x))
            |> info(...);
    }
}