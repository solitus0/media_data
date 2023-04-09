<?php

declare(strict_types=1);

namespace App\General\Model\Query;

use App\General\Enum\MediaFormatEnum;
use App\Shared\Validator\ExtraField;
use JMS\Serializer\Annotation as Serializer;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

#[ExtraField]
class FilenameQuery
{
    #[Assert\NotBlank]
    #[Serializer\Type(name: 'string')]
    private null|string $rawName = null;

    #[Assert\NotBlank]
    #[Serializer\Type(name: 'enum<App\General\Enum\MediaFormatEnum>')]
    #[OA\Property(ref: new Model(type: MediaFormatEnum::class))]
    private null|MediaFormatEnum $format = null;

    #[Serializer\Type(name: 'boolean')]
    #[OA\Property(default: false)]
    private null|bool $folderNamePriority = null;

    public function getRawName(): ?string
    {
        return $this->rawName;
    }

    public function getBasename(): string
    {
        return basename($this->rawName);
    }

    public function getFolderNamePriority(): ?bool
    {
        return $this->folderNamePriority;
    }

    public function getFolder(): null|string
    {
        $isPath = str_contains($this->rawName, '/');
        if (!$isPath) {
            return null;
        }

        $parentFolder = basename(dirname($this->rawName));
        if ($this->isSeasonFolder($parentFolder)) {
            $parentFolder = basename(dirname($this->rawName, 2));
        }

        if (!$this->isSeasonFolder($parentFolder)) {
            return $parentFolder;
        }

        return null;
    }

    private function isSeasonFolder(string $parentFolder): bool
    {
        return preg_match('/[Ss]\d+/', $parentFolder) === 1 ||
            str_starts_with(\strtolower($parentFolder), 'season');
    }

    public function getFormat(): ?MediaFormatEnum
    {
        return $this->format;
    }
}
