<?php

namespace App\Http\Admin\api;

use App\Domain\File\Service\FolderManager;
use App\Http\Admin\api\Payload\CreateFolderPayload;
use App\Http\Admin\api\Payload\DeletePayload;
use App\Http\Admin\api\Payload\RenamePayload;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/folders', name: 'api_folders_')]
class FoldersApi extends AbstractController
{
    public function __construct(private readonly FolderManager $folders)
    {
    }

    #[Route('', name: 'list', methods: ['GET'])]
    public function list(): JsonResponse
    {
        return $this->json($this->folders->list());
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function create(#[MapRequestPayload] CreateFolderPayload $payload): JsonResponse
    {
        return $this->json($this->folders->create($payload->name, $payload->parent), Response::HTTP_CREATED);
    }

    #[Route('', name: 'rename', methods: ['PATCH'])]
    public function rename(#[MapRequestPayload] RenamePayload $payload): JsonResponse
    {
        return $this->json($this->folders->rename($payload->id, $payload->name));
    }

    #[Route('', name: 'delete', methods: ['DELETE'])]
    public function delete(#[MapRequestPayload] DeletePayload $payload): JsonResponse
    {
        $this->folders->delete($payload->id);

        return $this->json(['message' => 'Folder deleted successfully']);
    }
}
