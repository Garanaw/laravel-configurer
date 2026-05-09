<?php

declare(strict_types=1);

use function Laravel\Prompts\alert;
use function Laravel\Prompts\error;
use function Laravel\Prompts\info;
use function Laravel\Prompts\note;
use function Laravel\Prompts\warning;

if (! function_exists('prompts_output')) {
    function prompts_output($type, $line): void {
        $writer = match ($type) {
            'info' => info(...),
            'warning' => warning(...),
            'alert' => alert(...),
            'error' => error(...),
            default => note(...),
        };

        $writer($line);
    }
}
