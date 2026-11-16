<?php

namespace App\Domain\File\Model;

final readonly class FileItem
{
    public function __construct(
        public string $id,
        public string $name,
        public ?string $parent,
        public string $extension,
        public string $type,
    ) {
    }
}
