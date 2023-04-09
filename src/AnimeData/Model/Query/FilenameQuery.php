<?php

declare(strict_types=1);

namespace App\AnimeData\Model\Query;

use App\General\Enum\MediaFormatEnum;
use App\Shared\Validator\ExtraField;
use JMS\Serializer\Annotation as Serializer;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

#[ExtraField]
class FilenameQuery
{
    public const TYPE_BASENAME = 'basename';
    public const TYPE_PATH = 'path';

    #[Assert\NotBlank]
    #[Serializer\Type(name: 'string')]
    private null|string $rawName = null;

    #[Assert\Choice(choices: [self::TYPE_BASENAME, self::TYPE_PATH])]
    #[Assert\NotBlank]
    #[Serializer\Type(name: 'string')]
    #[OA\Property(default: self::TYPE_PATH)]
    private null|string $type = self::TYPE_PATH;

    #[Serializer\Type(name: 'boolean')]
    #[OA\Property(default: false)]
    private null|bool $folderNamePriority = null;

    #[Serializer\Type(name: 'enum<App\General\Enum\MediaFormatEnum>')]
    #[OA\Property(ref: new Model(type: MediaFormatEnum::class))]
    private null|MediaFormatEnum $format = null;

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

    public function getFolderNamePriority(): ?bool
    {
        return $this->folderNamePriority;
    }

    public function setFolderNamePriority(?bool $folderNamePriority): void
    {
        $this->folderNamePriority = $folderNamePriority;
    }

    public function getFolder(): null|string
    {
        if ($this->type === self::TYPE_PATH) {
            $parentFolder = basename(dirname($this->rawName));
            if ($this->isSeasonFolder($parentFolder)) {
                $parentFolder = basename(dirname($this->rawName, 2));
            }

            if (!$this->isSeasonFolder($parentFolder)) {
                return $parentFolder;
            }
        }

        return null;
    }

    private function isSeasonFolder(string $parentFolder): bool
    {
        return preg_match('/[Ss]\d+/', $parentFolder) === 1 ||
            str_starts_with(\strtolower($parentFolder), 'season');
    }
}
