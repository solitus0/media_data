<?php

declare(strict_types=1);

namespace App\Shared\Exception\Trait;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\ConstraintViolationListInterface;

trait ProcessViolationListErrorsTrait
{
    use ApiExceptionTrait;

    public function processErrors(ConstraintViolationListInterface $errors, string $errorType = null): void
    {
        if (count($errors) > 0) {
            $result = [];
            foreach ($errors as $error) {
                $path = $error->getPropertyPath();
                if (empty($path)) {
                    $path = '*';
                }

                $result['errors'][$path][] = $error->getMessage();
            }

            throw $this->createApiException(Response::HTTP_BAD_REQUEST, 'Validation error', $errorType, $result);
        }
    }

    public function logErrors(
        LoggerInterface $logger,
        ConstraintViolationListInterface $errors,
    ): void {
        if (count($errors) > 0) {
            $result = [];
            foreach ($errors as $error) {
                $path = $error->getPropertyPath();
                if (empty($path)) {
                    $path = '*';
                }

                $result['errors'][$path][] = $error->getMessage();
            }

            $logger->error(message: 'Validation errors', context: $result);
        }
    }
}
