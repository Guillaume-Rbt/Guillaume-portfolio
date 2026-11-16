<?php

namespace App\Domain\File\Exception;

class FileConflictException extends FileStorageException
{
    public function __construct(string $name)
    {
        parent::__construct(sprintf('Un fichier nomme "%s" existe deja ici.', $name), 409);
    }
}
