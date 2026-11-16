<?php

namespace App\Domain\File\Model;

final readonly class FolderItem
{
    public function __construct(
        public string $id,
        public string $name,
        public ?string $parent,
    ) {
    }
}
