<?php

namespace App\Domain\File\Service;

use App\Domain\File\Exception\FileConflictException;
use App\Domain\File\Exception\FileNotFoundException;
use App\Domain\File\Exception\FileTooLargeException;
use App\Domain\File\Exception\UploadFailedException;
use App\Domain\File\Model\FileItem;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Mime\MimeTypes;

/**
 * Entry point for everything related to files stored in the uploads directory.
 * It is independent of the HTTP layer: the API controllers as well as any form
 * handling an UploadedFile can rely on it (see store() / storeMany()).
 */
class FileManager
{
    public function __construct(
        private readonly StoragePathResolver $paths,
        private readonly FolderManager $folders,
        private readonly Filesystem $filesystem,
        #[Autowire('%app.upload_max_size%')] private readonly int $maxSize,
    ) {
    }

    /**
     * Files directly contained in a folder (not recursive).
     *
     * @return list<FileItem>
     */
    public function list(?string $parent = null): array
    {
        $parent = $this->folders->assertExists($parent);

        $files = [];
        foreach ((new Finder())->in($this->paths->absolute($parent))->depth(0)->files()->ignoreDotFiles(false)->sortByName() as $file) {
            $files[] = $this->toItem($this->paths->join($parent, $file->getFilename()));
        }

        return $files;
    }

    public function get(string $id): FileItem
    {
        return $this->toItem($this->assertExists($id));
    }

    public function store(UploadedFile $file, ?string $parent = null): FileItem
    {
        return $this->storeMany([$file], $parent)[0];
    }

    /**
     * Stores every file or none of them if one is invalid or would overwrite an existing file.
     *
     * @param iterable<UploadedFile> $files
     *
     * @return list<FileItem>
     */
    public function storeMany(iterable $files, ?string $parent = null): array
    {
        $parent = $this->folders->assertExists($parent);
        $directory = $this->paths->absolute($parent);

        $names = [];
        foreach ($files as $file) {
            $name = $this->validateUpload($file);
            if (isset($names[$name]) || file_exists($directory.'/'.$name)) {
                throw new FileConflictException($name);
            }
            $names[$name] = $file;
        }

        $items = [];
        foreach ($names as $name => $file) {
            try {
                $file->move($directory, (string) $name);
            } catch (FileException $e) {
                throw new UploadFailedException(sprintf('Impossible d\'enregistrer le fichier "%s".', $name), $e);
            }
            $items[] = $this->toItem($this->paths->join($parent, (string) $name));
        }

        return $items;
    }

    public function rename(string $id, string $name): FileItem
    {
        $id = $this->assertExists($id);
        $this->paths->assertValidName($name);

        $newId = $this->paths->join($this->paths->parentOf($id), $name);
        if ($newId === $id) {
            return $this->toItem($id);
        }

        if (file_exists($this->paths->absolute($newId))) {
            throw new FileConflictException($name);
        }

        $this->filesystem->rename($this->paths->absolute($id), $this->paths->absolute($newId));

        return $this->toItem($newId);
    }

    public function move(string $id, ?string $parent): FileItem
    {
        $id = $this->assertExists($id);
        $parent = $this->folders->assertExists($parent);

        $name = basename($id);
        $newId = $this->paths->join($parent, $name);
        if ($newId === $id) {
            return $this->toItem($id);
        }

        if (file_exists($this->paths->absolute($newId))) {
            throw new FileConflictException($name);
        }

        $this->filesystem->rename($this->paths->absolute($id), $this->paths->absolute($newId));

        return $this->toItem($newId);
    }

    /**
     * Removing a file that does not exist is a no-op.
     */
    public function delete(string $id): void
    {
        $path = $this->paths->absolute($id);
        if (is_file($path)) {
            $this->filesystem->remove($path);
        }
    }

    /**
     * @return string the validated client file name
     */
    private function validateUpload(UploadedFile $file): string
    {
        $name = $file->getClientOriginalName();

        if (!$file->isValid()) {
            throw match ($file->getError()) {
                \UPLOAD_ERR_INI_SIZE, \UPLOAD_ERR_FORM_SIZE => new FileTooLargeException($name),
                default => new UploadFailedException($file->getErrorMessage()),
            };
        }

        if ($file->getSize() > $this->maxSize) {
            throw new FileTooLargeException($name);
        }

        $this->paths->assertValidName($name);

        return $name;
    }

    private function assertExists(string $id): string
    {
        $id = $this->paths->normalize($id);
        if ('' === $id || !is_file($this->paths->absolute($id))) {
            throw new FileNotFoundException();
        }

        return $id;
    }

    private function toItem(string $id): FileItem
    {
        $path = $this->paths->absolute($id);

        return new FileItem(
            id: $id,
            name: basename($id),
            parent: $this->paths->parentOf($id),
            extension: pathinfo($id, \PATHINFO_EXTENSION),
            type: MimeTypes::getDefault()->guessMimeType($path) ?? 'application/octet-stream',
        );
    }
}
