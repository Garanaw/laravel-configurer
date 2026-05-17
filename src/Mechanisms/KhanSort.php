<?php

declare(strict_types=1);

namespace Garanaw\LaravelConfigurer\Mechanisms;

use Garanaw\LaravelConfigurer\Contracts\InstallCommand;
use Illuminate\Support\Enumerable;
use RuntimeException;

class KhanSort
{
    /** @param Enumerable<InstallCommand> $commands */
    public function sort(Enumerable $commands): Enumerable
    {
        $graph = [];
        $inDegree = [];

        foreach ($commands as $command) {
            $id = $command->command();

            $graph[$id] = [];
            $inDegree[$id] = 0;
        }

        // Make graph
        foreach ($commands as $command) {
            $id = $command->command();
            $dependencies = $command->dependsOn() ?? [];

            foreach ($dependencies as $dep) {
                if (!isset($graph[$dep])) {
                    throw new RuntimeException("Dependency not found: {$dep}");
                }

                // dep -> id
                $graph[$dep][] = $id;
                $inDegree[$id]++;
            }
        }

        // Initial queue without dependencies
        $queue = [];

        foreach ($inDegree as $id => $degree) {
            if ($degree === 0) {
                $queue[] = $id;
            }
        }

        $sorted = [];

        while (!empty($queue)) {
            $current = array_shift($queue);
            $sorted[] = $current;

            foreach ($graph[$current] as $neighbor) {
                $inDegree[$neighbor]--;

                if ($inDegree[$neighbor] === 0) {
                    $queue[] = $neighbor;
                }
            }
        }

        // Detect cycles
        if (count($sorted) !== count($commands)) {
            throw new RuntimeException('Dependencias circulares detectadas');
        }

        // Map to instances
        $map = [];
        foreach ($commands as $command) {
            $map[$command->command()] = $command;
        }

        return collect($sorted)->map(static fn ($id) => $map[$id]);
    }
}