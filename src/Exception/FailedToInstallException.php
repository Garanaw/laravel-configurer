<?php

declare(strict_types=1);

namespace Garanaw\LaravelConfigurer\Exception;

use Garanaw\LaravelConfigurer\Library;
use Symfony\Component\HttpFoundation\Response;

class FailedToInstallException extends \RuntimeException
{
    private Library $library;

    public function __construct(Library $library, ?\Throwable $previous = null)
    {
        parent::__construct(
            sprintf('Library %s failed to install', $library->name),
            Response::HTTP_INTERNAL_SERVER_ERROR,
            $previous,
        );

        $this->library = $library;
    }

    public static function fromLibrary(Library $library, ?\Throwable $previous = null): self
    {
        return new self($library, $previous);
    }

    public function getLibrary(): Library
    {
        return $this->library;
    }
}
