<?php

declare(strict_types=1);

namespace Garanaw\LaravelConfigurer\Console\Commands;

use Illuminate\Config\Repository;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Command;
use Illuminate\Support\Enumerable;
use Symfony\Component\Console\Attribute\AsCommand;

use function Laravel\Prompts\table;
use function Laravel\Prompts\warning;

#[AsCommand('configurer:show')]
#[Description('Shows application configuration')]
class Show extends Command
{
    protected $signature = 'configurer:show
                            {--tags=* : Show the libraries that contain the given tags.}';

    public function handle(
        Repository $config
    ): void {
        $allLibraries = collect($config->get('configurer.libraries', []))
            ->when(
                $this->option('tags'),
                fn (Enumerable $libraries) => $libraries->filter(
                    fn (array $library) => collect($library['tags'] ?? [])->intersect($this->option('tags'))->isNotEmpty()
                )
            );

        if ($allLibraries->isEmpty()) {
            warning('No libraries found with the given tags.');

            return;
        }

        $map = $allLibraries->map(static fn (array $library) => [
            'Library' => $library['name'],
            'Tags' => implode(', ', $library['tags']),
            'HasMigrations' => $library['needsMigrating'] ?? false,
            'HasEnvVars' => array_key_exists('envVars', $library) ?? false,
            'GitHub' => $library['github'],
        ]);

        table(
            headers: ['Library', 'Tags', 'Has Migrations', 'Has Env Vars', 'GitHub'],
            rows: $map->values()->all(),
        );
    }
}