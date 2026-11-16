<?php

namespace App\Domain\File\Exception;

class InvalidPathException extends FileStorageException
{
    public function __construct(string $message = 'Chemin ou nom invalide.')
    {
        parent::__construct($message, 400);
    }
}
