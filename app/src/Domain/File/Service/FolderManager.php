<?php

namespace App\Domain\File\Service;

use App\Domain\File\Exception\FolderConflictException;
use App\Domain\File\Exception\FolderNotFoundException;
use App\Domain\File\Exception\InvalidPathException;
use App\Domain\File\Model\FolderItem;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

class FolderManager
{
    public function __construct(
        private readonly StoragePathResolver $paths,
        private readonly Filesystem $filesystem,
    ) {
    }

    /**
     * Every sub-folder of the root (recursively), the root itself excluded.
     *
     * @return list<FolderItem>
     */
    public function list(): array
    {
        $root = $this->paths->getRoot();
        if (!is_dir($root)) {
            return [];
        }

        $folders = [];
        foreach ((new Finder())->in($root)->directories()->ignoreDotFiles(false)->sortByName() as $directory) {
            $folders[] = $this->toItem($directory->getRelativePathname());
        }

        return $folders;
    }

    public function create(string $name, ?string $parent = null): FolderItem
    {
        $this->paths->assertValidName($name);
        $parent = $this->assertExists($parent);

        $id = $this->paths->join($parent, $name);
        $path = $this->paths->absolute($id);
        if (file_exists($path)) {
            throw new FolderConflictException($name);
        }

        $this->filesystem->mkdir($path);

        return $this->toItem($id);
    }

    public function rename(string $id, string $name): FolderItem
    {
        $id = $this->assertExists($id);
        $this->paths->assertValidName($name);

        $newId = $this->paths->join($this->paths->parentOf($id), $name);
        if ($newId === $id) {
            return $this->toItem($id);
        }

        if (file_exists($this->paths->absolute($newId))) {
            throw new FolderConflictException($name);
        }

        $this->filesystem->rename($this->paths->absolute($id), $this->paths->absolute($newId));

        return $this->toItem($newId);
    }

    /**
     * Recursively removes a folder. Removing a folder that does not exist is a no-op.
     */
    public function delete(string $id): void
    {
        $id = $this->paths->normalize($id);
        if ('' === $id) {
            throw new InvalidPathException('Le dossier racine ne peut pas etre supprime.');
        }

        $path = $this->paths->absolute($id);
        if (is_dir($path)) {
            $this->filesystem->remove($path);
        }
    }

    /**
     * The root folder is created on demand, so it always exists.
     *
     * @return string the normalized identifier
     */
    public function assertExists(?string $id): string
    {
        $id = $this->paths->normalize($id);
        if ('' === $id) {
            $this->filesystem->mkdir($this->paths->getRoot());
        }

        if (!is_dir($this->paths->absolute($id))) {
            throw new FolderNotFoundException();
        }

        return $id;
    }

    private function toItem(string $id): FolderItem
    {
        return new FolderItem($id, basename($id), $this->paths->parentOf($id));
    }
}
