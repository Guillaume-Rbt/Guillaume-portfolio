<?php

namespace App\Domain\File\Exception;

class FileTooLargeException extends FileStorageException
{
    public function __construct(string $name)
    {
        parent::__construct(sprintf('Le fichier "%s" est trop volumineux.', $name), 413);
    }
}
