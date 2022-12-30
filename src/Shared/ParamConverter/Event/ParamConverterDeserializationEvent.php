<?php

declare(strict_types=1);

namespace App\Shared\ParamConverter\Event;

use Symfony\Contracts\EventDispatcher\Event;

class ParamConverterDeserializationEvent extends Event
{
    public function __construct(
        private readonly string $objectClass,
        private readonly array $data,
    ) {}

    public function getObjectClass(): string
    {
        return $this->objectClass;
    }

    public function getData(): array
    {
        return $this->data;
    }
}
