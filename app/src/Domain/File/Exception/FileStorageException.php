<?php

namespace App\Domain\File\Exception;

/**
 * Base class for every expected error raised by the file storage.
 * The HTTP status code is carried by the exception so it can be reused
 * by the API as well as by any other caller (forms, commands...).
 */
abstract class FileStorageException extends \RuntimeException
{
    public function __construct(string $message, private readonly int $statusCode, ?\Throwable $previous = null)
    {
        parent::__construct($message, 0, $previous);
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }
}
