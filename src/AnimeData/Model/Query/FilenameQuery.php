<?php

declare(strict_types=1);

namespace App\AnimeData\Model\Query;

use App\Shared\Validator\ExtraField;
use JMS\Serializer\Annotation as Serializer;
use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

#[ExtraField]
class FilenameQuery
{
    public const TYPE_BASENAME = 'basename';
    public const TYPE_PATH = 'path';

    #[Assert\NotBlank]
    #[Serializer\Type(name: 'string')]
    private ?string $rawName = null;

    #[Assert\Choice(choices: [self::TYPE_BASENAME, self::TYPE_PATH])]
    #[Assert\NotBlank]
    #[Serializer\Type(name: 'string')]
    #[OA\Property(default: self::TYPE_PATH)]
    private ?string $type = self::TYPE_PATH;

    public function getRawName(): ?string
    {
        return $this->rawName;
    }

    public function setRawName(?string $rawName): self
    {
        $this->rawName = $rawName;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getBasename(): string
    {
        if ($this->type === self::TYPE_PATH) {
            return basename($this->rawName);
        }

        return $this->rawName;
    }
}
