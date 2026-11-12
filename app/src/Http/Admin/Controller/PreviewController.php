<?php

namespace App\Http\Admin\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class PreviewController extends AbstractController
{
    #[Route('/preview', name: 'admin_preview')]
    public function preview(Request $request): Response
    {
        $params = $request->query->all();

        try {
            $data = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            throw new BadRequestHttpException('Corps JSON invalide.');
        }

        if (!is_array($data)) {
            throw new BadRequestHttpException('Corps JSON invalide.');
        }

        if (!array_is_list($data)) {
            $name = $data['_name'] ?? null;

            if (!is_string($name) || !preg_match('/^[a-z][a-z0-9_-]*$/i', $name)) {
                throw new BadRequestHttpException('Clé "_name" invalide.');
            }

            return $this->render('components/' . $name . '.html.twig', [
                'data' => $data['data'] ?? [],
                'config' => $data['config'] ?? [],
                "requestParams" => $params,
            ]);
        }


        return $this->render('admin/preview/preview.html.twig', [
            "requestParams" => $params,
            'blocks' => $data,
        ]);
    }
}
