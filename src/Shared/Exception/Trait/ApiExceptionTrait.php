<?php

declare(strict_types=1);

namespace App\Shared\Exception\Trait;

use App\Shared\Exception\Response\ApiException;
use App\Shared\Exception\Response\ApiExceptionDetail;

trait ApiExceptionTrait
{
    protected function createApiException(
        int $status,
        string|object $detail,
        string $errorType = null,
        array $arbitraryData = []
    ): ApiException {
        $apiException = new ApiExceptionDetail(
            status: $status,
            detail: $detail,
            errorType: $errorType,
            arbitraryData: $arbitraryData,
        );

        return new ApiException(apiException: $apiException);
    }
}
