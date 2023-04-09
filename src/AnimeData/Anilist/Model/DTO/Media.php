<?php

declare(strict_types=1);

namespace App\AnimeData\Anilist\Model\DTO;

use App\Shared\Util\ArrayPropertyUtil;
use JMS\Serializer\Annotation as Serializer;

class Media
{
    #[Serializer\Type(name: 'int')]
    private ?int $id = null;

    #[Serializer\Type(name: 'int')]
    private ?int $idMal = null;

    #[Serializer\Type(name: 'string')]
    private ?string $format = null;

    #[Serializer\Type(name: 'string')]
    private ?string $type = null;

    #[Serializer\Type(name: 'array')]
    private ?array $genres = null;

    #[Serializer\Type(name: 'string')]
    private ?string $description = null;

    #[Serializer\Type(name: 'string')]
    private ?string $episodes = null;

    #[Serializer\Type(name: 'string')]
    private ?string $season = null;

    #[Serializer\Type(name: 'string')]
    private ?string $seasonYear = null;

    #[Serializer\Type(name: 'string')]
    private ?string $duration = null;

    #[Serializer\Type(name: 'array')]
    private ?array $startDate = null;

    #[Serializer\Type(name: 'array')]
    private ?array $endDate = null;

    #[Serializer\Type(name: 'array')]
    private ?array $title = null;

    #[Serializer\Type(name: 'array')]
    private ?array $tags = null;

    #[Serializer\Type(name: 'bool')]
    private ?bool $isAdult = null;

    #[Serializer\Type(name: 'string')]
    private ?string $countryOfOrigin = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getIdMal(): ?int
    {
        return $this->idMal;
    }

    public function setIdMal(?int $idMal): void
    {
        $this->idMal = $idMal;
    }

    public function getFormat(): ?string
    {
        return $this->format;
    }

    public function setFormat(?string $format): void
    {
        $this->format = $format;
    }

    public function getGenres(): ?array
    {
        return $this->genres;
    }

    public function setGenres(?array $genres): void
    {
        $this->genres = $genres;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function getEpisodes(): ?string
    {
        return $this->episodes;
    }

    public function setEpisodes(?string $episodes): void
    {
        $this->episodes = $episodes;
    }

    public function getSeason(): ?string
    {
        return $this->season;
    }

    public function setSeason(?string $season): void
    {
        $this->season = $season;
    }

    public function getSeasonYear(): ?string
    {
        return $this->seasonYear;
    }

    public function setSeasonYear(?string $seasonYear): void
    {
        $this->seasonYear = $seasonYear;
    }

    public function getDuration(): ?string
    {
        return $this->duration;
    }

    public function setDuration(?string $duration): void
    {
        $this->duration = $duration;
    }

    public function getStartDate(): ?array
    {
        return $this->startDate;
    }

    public function setStartDate(?array $startDate): void
    {
        $this->startDate = $startDate;
    }

    public function getEndDate(): ?array
    {
        return $this->endDate;
    }

    public function setEndDate(?array $endDate): void
    {
        $this->endDate = $endDate;
    }

    public function getTitles(): ?array
    {
        return $this->title;
    }

    public function getTags(): ?array
    {
        return $this->tags;
    }

    public function setTags(?array $tags): void
    {
        $this->tags = $tags;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): void
    {
        $this->type = $type;
    }

    public function getTitle($lang = 'english'): ?string
    {
        $title = ArrayPropertyUtil::getProperty($this->title, $lang);
        if ($title) {
            return $title;
        }

        return ArrayPropertyUtil::getProperty($this->title, 'romaji');
    }

    public function setTitle(?string $title): void
    {
        $this->title = $title;
    }

    public function getIsAdult(): ?bool
    {
        return $this->isAdult;
    }

    public function setIsAdult(?bool $isAdult): self
    {
        $this->isAdult = $isAdult;

        return $this;
    }

    public function getCountryOfOrigin(): ?string
    {
        return $this->countryOfOrigin;
    }

    public function setCountryOfOrigin(?string $countryOfOrigin): self
    {
        $this->countryOfOrigin = $countryOfOrigin;

        return $this;
    }
}
