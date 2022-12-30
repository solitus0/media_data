<?php

declare(strict_types=1);

namespace App\Shared\Exception\Response;

use Exception;

class ApiExceptionDetail
{
    private array $problemStatusTitles = [
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Time-out',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Large',
        415 => 'Unsupported Media Type',
        416 => 'Requested range not satisfiable',
        417 => 'Expectation Failed',
        418 => 'I\'m a teapot',
        422 => 'Unprocessable Entity',
        423 => 'Locked',
        424 => 'Failed Dependency',
        425 => 'Unordered Collection',
        426 => 'Upgrade Required',
        428 => 'Precondition Required',
        429 => 'Too Many Requests',
        431 => 'Request Header Fields Too Large',
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Time-out',
        505 => 'HTTP Version not supported',
        506 => 'Variant Also Negotiates',
        507 => 'Insufficient Storage',
        508 => 'Loop Detected',
        511 => 'Network Authentication Required',
    ];

    public function __construct(
        private ?int $status,
        private readonly object|string|null $detail,
        private readonly ?string $errorType = null,
        private readonly ?array $arbitraryData = []
    ) {}

    public function toArray(): array
    {
        return array_merge(
            $this->arbitraryData,
            [
                'ErrorType' => $this->getErrorType(),
                'status' => $this->getStatus(),
                'ErrorMessage' => $this->getErrorMessage(),
            ]
        );
    }

    public function getErrorType()
    {
        if (null !== $this->errorType) {
            return $this->errorType;
        }

        if ($this->detail instanceof Exception) {
            return get_class($this->detail);
        }

        $status = $this->getStatus();
        if (array_key_exists($status, $this->problemStatusTitles)) {
            return $this->problemStatusTitles[$status];
        }

        return 'Unknown';
    }

    public function getStatus(): int
    {
        if ($this->detail instanceof Exception && is_numeric($this->detail->getCode())) {
            $this->status = $this->detail->getCode();
        }

        if ($this->status < 100 || $this->status > 599) {
            $this->status = 500;
        }

        return $this->status;
    }

    public function getErrorMessage(): string
    {
        return $this->detail instanceof Exception ? $this->detail->getMessage() : $this->detail;
    }
}
