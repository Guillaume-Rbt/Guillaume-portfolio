<?php

namespace App\Http\Admin\api;

use App\Domain\File\Service\FileManager;
use App\Http\Admin\api\Payload\DeletePayload;
use App\Http\Admin\api\Payload\MoveFilePayload;
use App\Http\Admin\api\Payload\RenamePayload;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/files', name: 'api_files_')]
class FilesUploadApi extends AbstractController
{
    public function __construct(private readonly FileManager $files)
    {
    }

    #[Route('', name: 'list', methods: ['GET'])]
    public function list(Request $request): JsonResponse
    {
        return $this->json($this->files->list($request->query->getString('parent') ?: null));
    }

    #[Route('', name: 'upload', methods: ['POST'])]
    public function upload(Request $request): JsonResponse
    {
        $uploads = $this->collectUploadedFiles($request->files->all());
        if ([] === $uploads) {
            return $this->json(['message' => 'Aucun fichier recu.'], Response::HTTP_BAD_REQUEST);
        }

        $files = $this->files->storeMany($uploads, $request->request->getString('parent') ?: null);

        return $this->json(['files' => $files], Response::HTTP_CREATED);
    }

    #[Route('', name: 'rename', methods: ['PATCH'])]
    public function rename(#[MapRequestPayload] RenamePayload $payload): JsonResponse
    {
        return $this->json($this->files->rename($payload->id, $payload->name));
    }

    #[Route('', name: 'delete', methods: ['DELETE'])]
    public function delete(#[MapRequestPayload] DeletePayload $payload): JsonResponse
    {
        $this->files->delete($payload->id);

        return $this->json(['file' => $payload->id]);
    }

    #[Route('/move', name: 'move', methods: ['POST'])]
    public function move(#[MapRequestPayload] MoveFilePayload $payload): JsonResponse
    {
        return $this->json($this->files->move($payload->id, $payload->parent));
    }

    /**
     * Any uploaded file is accepted, whatever the name of its form field.
     *
     * @return list<UploadedFile>
     */
    private function collectUploadedFiles(array $files): array
    {
        $uploads = [];
        foreach ($files as $file) {
            if ($file instanceof UploadedFile) {
                $uploads[] = $file;
            } elseif (\is_array($file)) {
                array_push($uploads, ...$this->collectUploadedFiles($file));
            }
        }

        return $uploads;
    }
}
