<?php

declare(strict_types=1);

namespace App\Shared\Exception\Response;

use Exception;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ApiException extends HttpException
{
    private ApiExceptionDetail $apiException;

    public function __construct(
        ApiExceptionDetail $apiException,
        Exception $previous = null,
        array $headers = [],
        $code = 0
    ) {
        $this->apiException = $apiException;
        $statusCode = $apiException->getStatus();
        $message = $apiException->getErrorMessage();
        parent::__construct($statusCode, $message, $previous, $headers, $code);
    }

    public function getApiException(): ApiExceptionDetail
    {
        return $this->apiException;
    }
}
