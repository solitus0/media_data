<?php

declare(strict_types=1);

namespace App\AnimeData\Model\DTO;

use JMS\Serializer\Annotation as Serializer;
use JMS\Serializer\Annotation\ExclusionPolicy;

#[Serializer\ExclusionPolicy(ExclusionPolicy::ALL)]
class MediaName
{
    public function __construct(
        private readonly null|string $format = null,
        private readonly null|string $title = null,
        private readonly null|string $extension = null,
        private readonly null|string $year = null,
        private null|int|string $season = null,
        private null|int|string $episode = null,
    ) {}

    #[Serializer\VirtualProperty]
    #[Serializer\SerializedName('fileName')]
    #[Serializer\Expose]
    #[Serializer\Groups(['Default'])]
    #[Serializer\Type('string')]
    public function getFilename(): ?string
    {
        return sprintf('%s%s', $this->getBasename(), $this->extension);
    }

    public function getBasename(): ?string
    {
        if ($this->format === 'MOVIE' && $this->year) {
            return sprintf('%s %s', $this->title, $this->year);
        }

        if ($this->format === 'MOVIE') {
            return sprintf('%s', $this->title);
        }

        if ($this->season && $this->getEpisode()) {
            return sprintf('%s %s%s', $this->title, $this->getSeason(), $this->getEpisode());
        }

        if ($this->episode) {
            return sprintf('%s %s', $this->title, $this->getEpisode());
        }

        return sprintf('%s', $this->title);
    }
    #[Serializer\VirtualProperty]
    #[Serializer\SerializedName('folderName')]
    #[Serializer\Expose]
    #[Serializer\Groups(['Default'])]
    #[Serializer\Type('string')]
    public function getFolderName(): string
    {
        if ($this->getSeason()) {
            return sprintf('%s/%s', $this->title, $this->getSeason());
        }

        return $this->title;
    }

    protected function getEpisode(): int|string|null
    {
        if (is_string($this->episode) && !str_contains(strtolower($this->episode), 'e')) {
            $this->episode = sprintf('E%s', $this->episode);
        }

        return $this->episode;
    }

    protected function getSeason(): int|string|null
    {
        if (is_string($this->season) && !str_contains(strtolower($this->season), 's')) {
            $this->season = sprintf('S%s', $this->season);
        }

        return $this->season;
    }
}
