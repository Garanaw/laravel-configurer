<?php

declare(strict_types=1);

namespace Garanaw\LaravelConfigurer\Tests\Unit\Console\Commands;

use Garanaw\LaravelConfigurer\Console\Commands\Show;

describe(Show::class, function () {
    it('will show all available libraries in config', function () {
        $testLibraries = [
            [
                'name' => 'Test Library',
                'command' => 'test/library',
                'tags' => ['test'],
                'github' => 'https://github.com/test',
            ],
        ];
        mockConfig(['libraries' => $testLibraries]);

        $this->artisan('configurer:show')->expectsPromptsTable(
            headers: ['Library', 'Tags', 'Has Migrations', 'Has Env Vars', 'GitHub'],
            rows: [[
                $testLibraries[0]['name'],
                implode(', ', $testLibraries[0]['tags']),
                no(),
                no(),
                $testLibraries[0]['github'],
            ]],
        );
    });

    it('will determine when a library needs migrating', function () {
        $testLibraries = [
            [
                'name' => 'Test Library',
                'command' => 'test/library',
                'needsMigrating' => true,
                'tags' => ['test'],
                'github' => 'https://github.com/test',
            ],
        ];
        mockConfig(['libraries' => $testLibraries]);

        $this->artisan('configurer:show')->expectsPromptsTable(
            headers: ['Library', 'Tags', 'Has Migrations', 'Has Env Vars', 'GitHub'],
            rows: [[
                $testLibraries[0]['name'],
                implode(', ', $testLibraries[0]['tags']),
                yes(),
                no(),
                $testLibraries[0]['github'],
            ]],
        );
    });

    it('will determine when a library has environment variables', function () {
        $testLibraries = [[
            'name' => 'Test Library',
            'command' => 'test/library',
            'envVars' => ['VAR' => 'Test Environment Variable'],
            'tags' => ['test'],
            'github' => 'https://github.com/test',
        ]];
        mockConfig(['libraries' => $testLibraries]);

        $this->artisan('configurer:show')->expectsPromptsTable(
            headers: ['Library', 'Tags', 'Has Migrations', 'Has Env Vars', 'GitHub'],
            rows: [[
                $testLibraries[0]['name'],
                implode(', ', $testLibraries[0]['tags']),
                no(),
                yes(),
                $testLibraries[0]['github'],
            ]],
        );
    });

    it('will determine when a library needs migrating AND has environment variables', function () {
        $testLibraries = [[
            'name' => 'Test Library',
            'command' => 'test/library',
            'envVars' => ['VAR' => 'Test Environment Variable'],
            'needsMigrating' => true,
            'tags' => ['test'],
            'github' => 'https://github.com/test',
        ]];
        mockConfig(['libraries' => $testLibraries]);

        $this->artisan('configurer:show')->expectsPromptsTable(
            headers: ['Library', 'Tags', 'Has Migrations', 'Has Env Vars', 'GitHub'],
            rows: [[
                $testLibraries[0]['name'],
                implode(', ', $testLibraries[0]['tags']),
                yes(),
                yes(),
                $testLibraries[0]['github'],
            ]]
        );
    });

    it('will filter only libraries that contain migrations', function () {
        $testLibraries = [
            [
                'name' => 'Test Library',
                'command' => 'test/library',
                'needsMigrating' => true,
                'tags' => ['migrate'],
                'github' => 'https://github.com/test',
            ],
            [
                'name' => 'No migrations library',
                'command' => 'test/no-migrate',
                'tags' => ['nop'],
                'github' => 'https://github.com/nop',
            ],
        ];
        mockConfig(['libraries' => $testLibraries]);

        $this->artisan('configurer:show --with-migrations')->expectsPromptsTable(
            headers: ['Library', 'Tags', 'Has Migrations', 'Has Env Vars', 'GitHub'],
            rows: [[
                $testLibraries[0]['name'],
                implode(', ', $testLibraries[0]['tags']),
                yes(),
                no(),
                $testLibraries[0]['github'],
            ]]
        );
    });

    it('will filter libraries by the specified tags', function () {
        $testLibraries = [
            [
                'name' => 'Test Library',
                'command' => 'test/library',
                'tags' => ['target'],
                'github' => 'https://github.com/test',
            ],
            [
                'name' => 'No target library',
                'command' => 'test/no-target',
                'tags' => ['nop'],
                'github' => 'https://github.com/nop',
            ]
        ];
        mockConfig(['libraries' => $testLibraries]);

        $this->artisan('configurer:show --tags=target')->expectsPromptsTable(
            headers: ['Library', 'Tags', 'Has Migrations', 'Has Env Vars', 'GitHub'],
            rows: [[
                $testLibraries[0]['name'],
                implode(', ', $testLibraries[0]['tags']),
                no(),
                no(),
                $testLibraries[0]['github'],
            ]],
        );
    });

    it('will accept multiple tags and will show them all', function () {
        $testLibraries = [
            [
                'name' => 'Test Library 1',
                'command' => 'test/library-1',
                'tags' => ['one'],
                'github' => 'https://github.com/test-1',
            ],
            [
                'name' => 'Test Library 2',
                'command' => 'test/library-2',
                'tags' => ['two'],
                'github' => 'https://github.com/test-2',
            ],
            [
                'name' => 'Test Library 3',
                'command' => 'test/library-3',
                'tags' => ['three'],
                'github' => 'https://github.com/test-3',
            ]
        ];
        mockConfig(['libraries' => $testLibraries]);

        $this->artisan('configurer:show --tags=one --tags=two')->expectsPromptsTable(
            headers: ['Library', 'Tags', 'Has Migrations', 'Has Env Vars', 'GitHub'],
            rows: [
                [$testLibraries[0]['name'], implode(', ', $testLibraries[0]['tags']), no(), no(), $testLibraries[0]['github']],
                [$testLibraries[1]['name'], implode(', ', $testLibraries[1]['tags']), no(), no(), $testLibraries[1]['github']],
            ]
        );
    });

    it('will warn the user when a non-existing tag is specified', function () {
        $testLibraries = [
            [
                'name' => 'Test Library',
                'command' => 'test/library',
                'tags' => ['target'],
                'github' => 'https://github.com/test',
            ]
        ];
        mockConfig(['libraries' => $testLibraries]);

        $this->artisan('configurer:show --tags=nope')
            ->expectsPromptsWarning('No libraries found with the given tags.');
    });

    it('will narrow down to tags and migrations when both are passed', function () {
        $testLibraries = [
            [
                'name' => 'Test Library',
                'command' => 'test/library',
                'needsMigrating' => true,
                'tags' => ['target'],
                'github' => 'https://github.com/test',
            ],
            [
                'name' => 'No migrations library',
                'command' => 'test/no-migrate',
                'tags' => ['nop'],
                'github' => 'https://github.com/nop',
            ],
            [
                'name' => 'No tags library',
                'command' => 'test/no-tags',
                'needsMigrating' => true,
                'tags' => ['nop'],
                'github' => 'https://github.com/nop',
            ]
        ];
        mockConfig(['libraries' => $testLibraries]);

        $this->artisan('configurer:show --tags=target --with-migrations')->expectsPromptsTable(
            headers: ['Library', 'Tags', 'Has Migrations', 'Has Env Vars', 'GitHub'],
            rows: [[
                $testLibraries[0]['name'],
                implode(', ', $testLibraries[0]['tags']),
                yes(),
                no(),
                $testLibraries[0]['github'],
            ]]
        );
    });
});
