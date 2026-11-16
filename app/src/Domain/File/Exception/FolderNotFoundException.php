<?php

namespace App\Domain\File\Exception;

class FolderNotFoundException extends FileStorageException
{
    public function __construct()
    {
        parent::__construct('Dossier introuvable.', 404);
    }
}
