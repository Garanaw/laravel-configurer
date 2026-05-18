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
        // Normalise entry (avoids issues with LazyCollection)
        $commands = collect($commands)->values();

        $graph = [];
        $inDegree = [];
        $map = [];
        $queue = [];
        $sorted = [];

        // 1. Index commands
        foreach ($commands as $command) {
            $id = $this->normalizeId($command->id());

            if (isset($map[$id])) {
                throw new RuntimeException("Duplicated ID detected: {$id}");
            }

            $map[$id] = $command;
            $graph[$id] = [];
            $inDegree[$id] = 0;
        }

        // 2. Build graph
        foreach ($commands as $command) {
            $id = $this->normalizeId($command->id());

            foreach ($command->dependsOn() as $depRaw) {
                $dependencyId = $this->normalizeId($depRaw);

                if (!isset($graph[$dependencyId])) {
                    throw new RuntimeException(
                        "Dependency '{$dependencyId}' not found. Required by '{$id}'"
                    );
                }

                // dep -> id
                $graph[$dependencyId][] = $id;
                $inDegree[$id]++;
            }
        }

        // 3. Init queue (nodes without dependencies)
        foreach ($inDegree as $id => $degree) {
            if ($degree === 0) {
                $queue[] = $id;
            }
        }

        $this->sortQueue($queue, $map); // determinism

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

            $this->sortQueue($queue, $map); // keep deterministic order
        }

        // 5. Detect cycles
        if (count($sorted) !== count($commands)) {
            $remaining = array_diff(array_keys($map), $sorted);

            throw new RuntimeException(
                'Circular dependencies detected between: ' . implode(', ', $remaining)
            );
        }

        // 6. Map result to instances
        return collect($sorted)->map(fn ($id) => $map[$id]);
    }

    protected function normalizeId(mixed $item): string
    {
        if ($item instanceof InstallCommand) {
            return $item->id() |> trim(...) |> strtolower(...);
        }

        if (! is_string($item)) {
            throw new RuntimeException(
                'Invalid dependency type: ' . get_debug_type($item)
            );
        }

        // Allow classes as dependencies (improved DX)
        if (
            class_exists($item) &&
            is_subclass_of($item, InstallCommand::class) &&
            method_exists($item, 'makeIdForDep')
        ) {
            return $item::makeIdForDep() |> trim(...) |> strtolower(...);
        }

        return $item |> trim(...) |> strtolower(...);
    }

    protected function sortQueue(array &$queue, array $map): void
    {
        usort($queue, static function ($a, $b) use ($map) {
            $weightA = $map[$a]->weight();
            $weightB = $map[$b]->weight();

            return $weightA <=> $weightB ?: $a <=> $b;
        });
    }
}
