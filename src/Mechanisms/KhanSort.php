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
        $map = [];

        // 1. Index
        foreach ($commands as $command) {
            $id = $command->id();

            if (isset($map[$id])) {
                throw new RuntimeException("ID duplicado detectado: {$id}");
            }

            $map[$id] = $command;
            $graph[$id] = [];
            $inDegree[$id] = 0;
        }

        // 2. Build graph
        foreach ($commands as $command) {
            $id = $command->id();

            foreach ($command->dependsOn() as $depId) {
                if (!isset($graph[$depId])) {
                    throw new RuntimeException(
                        "Dependencia no encontrada: {$depId} requerida por {$id}"
                    );
                }

                $graph[$depId][] = $id;
                $inDegree[$id]++;
            }
        }

        // 3. Init queue
        $queue = [];

        foreach ($inDegree as $id => $degree) {
            if ($degree === 0) {
                $queue[] = $id;
            }
        }

        $sorted = [];

        // 4. Kahn
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

        // 5. Detect cycles
        if (count($sorted) !== count($commands)) {
            $remaining = array_diff(array_keys($map), $sorted);

            throw new RuntimeException(
                'Dependencias circulares detectadas: ' . implode(', ', $remaining)
            );
        }

        // 6. Map result
        return collect($sorted)->map(static fn ($id) => $map[$id]);
    }
}