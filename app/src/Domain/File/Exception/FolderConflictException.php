<?php

namespace App\Domain\File\Exception;

class FolderConflictException extends FileStorageException
{
    public function __construct(string $name)
    {
        parent::__construct(sprintf('Un dossier nomme "%s" existe deja ici.', $name), 409);
    }
}
