<?php

declare(strict_types=1);

namespace App\Shared\Exception\Subscriber;

use App\Shared\Exception\Response\ApiException;
use App\Shared\Exception\Response\ApiExceptionDetail;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class ApiExceptionSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => ['onKernelException', 5],
        ];
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        $apiException = null;
        if ($exception instanceof ApiException) {
            $apiException = $exception->getApiException();
        }

        if (!$apiException) {
            $apiException = new ApiExceptionDetail(
                status: $exception->getCode(),
                detail: $exception->getMessage(),
            );
        }

        $response = new JsonResponse(
            $apiException->toArray(),
            $apiException->getStatus()
        );

        $event->setResponse($response);
    }
}
