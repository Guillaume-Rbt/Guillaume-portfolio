<?php

namespace App\Domain\File\Exception;

class FileNotFoundException extends FileStorageException
{
    public function __construct()
    {
        parent::__construct('Fichier introuvable.', 404);
    }
}
