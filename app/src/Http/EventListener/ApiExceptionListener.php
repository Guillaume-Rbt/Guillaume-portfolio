<?php

namespace App\Http\EventListener;

use App\Domain\File\Exception\FileStorageException;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

/**
 * Renders expected errors of the "api_*" routes as {"message": "..."}.
 * Other routes (forms using the file services...) handle those exceptions themselves.
 */
#[AsEventListener]
final class ApiExceptionListener
{
    public function __invoke(ExceptionEvent $event): void
    {
        $route = $event->getRequest()->attributes->get('_route');
        if (!\is_string($route) || !str_starts_with($route, 'api_')) {
            return;
        }

        $exception = $event->getThrowable();
        $status = match (true) {
            $exception instanceof FileStorageException, $exception instanceof HttpExceptionInterface => $exception->getStatusCode(),
            default => null,
        };

        if (null !== $status) {
            $event->setResponse(new JsonResponse(['message' => $exception->getMessage()], $status));
        }
    }
}
