<?php

namespace App\Domain\File\Service;

use App\Domain\File\Exception\InvalidPathException;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

/**
 * Translates the relative identifiers exposed to clients ("documents/a.pdf")
 * into absolute filesystem paths, guaranteeing they never leave the root directory.
 */
class StoragePathResolver
{
    public function __construct(
        #[Autowire('%uploads%')] private readonly string $root,
    ) {
    }

    public function getRoot(): string
    {
        return rtrim($this->root, '/');
    }

    /**
     * Normalizes an identifier: null, "" and "/" give "" (the root).
     */
    public function normalize(?string $id): string
    {
        $segments = [];
        foreach (explode('/', str_replace('\\', '/', $id ?? '')) as $segment) {
            if ('' === $segment || '.' === $segment) {
                continue;
            }
            $this->assertValidName($segment);
            $segments[] = $segment;
        }

        return implode('/', $segments);
    }

    public function absolute(string $relative): string
    {
        $relative = $this->normalize($relative);

        return '' === $relative ? $this->getRoot() : $this->getRoot().'/'.$relative;
    }

    /**
     * Identifier of the parent folder, or null for the root.
     */
    public function parentOf(string $relative): ?string
    {
        $parent = dirname($this->normalize($relative));

        return '.' === $parent ? null : $parent;
    }

    public function join(?string $parent, string $name): string
    {
        $parent = $this->normalize($parent);

        return '' === $parent ? $name : $parent.'/'.$name;
    }

    /**
     * Ensures a single path segment (file or folder name) is safe.
     */
    public function assertValidName(string $name): void
    {
        if (
            '' === trim($name)
            || '.' === $name
            || '..' === $name
            || 255 < strlen($name)
            || 1 === preg_match('#[/\\\\\x00-\x1F\x7F]#', $name)
        ) {
            throw new InvalidPathException();
        }
    }
}
