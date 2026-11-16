<?php

namespace App\Domain\File\Exception;

class UploadFailedException extends FileStorageException
{
    public function __construct(string $message, ?\Throwable $previous = null)
    {
        parent::__construct($message, 400, $previous);
    }
}
