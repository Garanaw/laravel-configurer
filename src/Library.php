<?php

declare(strict_types=1);

namespace Garanaw\LaravelConfigurer;

use Garanaw\LaravelConfigurer\Contracts\LibraryCommand;
use Illuminate\Support\Enumerable;
use Illuminate\Support\Fluent;

/**
 * @property-read string $name
 * @property-read string $command
 * @property-read Enumerable<LibraryCommand> $installCommands
 * @property-read ?list<string> $publishCommands
 * @property-read ?bool $needsMigrating
 * @property-read bool $canBeDevOnly
 * @property-read ?array{?provider: string, ?tags: list<string>} $publishCommands
 * @property-read ?array $envVars
 * @property bool $required
 * @property bool $published
 * @property bool $installed
 */
class Library extends Fluent
{
    public function __construct(
        string $name,
        string $command,
        Enumerable $installCommands,
        ?array $publishCommands = null,
        bool $needsMigrating = false,
        bool $canBeDevOnly = false,
        ?array $envVars = null,
    ) {
        parent::__construct([
            'name' => $name,
            'command' => $command,
            'installCommands' => $installCommands,
            'needsMigrating' => $needsMigrating,
            'canBeDevOnly' => $canBeDevOnly,
            'publishCommands' => $publishCommands,
            'envVars' => $envVars,
            'required' => false,
            'published' => false,
            'installed' => false,
        ]);
    }

    public function isRequired(): bool
    {
        return $this->required;
    }

    public function required(): void
    {
        $this->required = true;
    }

    public function isPublished(): bool
    {
        return $this->published;
    }

    public function published(): void
    {
        $this->published = true;
    }

    public function isInstalled(): bool
    {
        return $this->installed;
    }

    public function installed(): void
    {
        $this->installed = true;
    }

    public function hasInstallCommands(): bool
    {
        return $this->installCommands !== null;
    }

    public function hasPublishCommands(): bool
    {
        return $this->publishCommands !== null;
    }

    public function hasEnvVars(): bool
    {
        return $this->envVars !== null;
    }
}
