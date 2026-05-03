<?php

declare(strict_types=1);

namespace Garanaw\LaravelConfigurer\Enum;

enum When
{
    // Runs the configuration command at the start of the library installation. Ofter for custom preparation commands
    case START;
    // Runs the configuration command at the end of the library installation. Most of the commands will use this option
    case END;
    // Runs the configuration command at the end of the installation of all libraries.
    case END_ALL;

    public function is(self $when): bool
    {
        return $this === $when;
    }

    public function label(): string
    {
        return match ($this) {
            self::START => 'Start of installation',
            self::END => 'End of installation',
            self::END_ALL => 'End of all installations',
        };
    }
}
