<?php

declare(strict_types=1);

namespace App\General\Model\DTO;

use JMS\Serializer\Annotation as Serializer;

class Metadata
{
    #[Serializer\Type(name: 'string')]
    private null|string $basename = null;
    #[Serializer\Type(name: 'array<string>')]
    private null|array $genres = null;
    #[Serializer\Type(name: 'array<string, string>')]
    private null|array $titles = null;

    #[Serializer\Type(name: 'array')]
    private null|array $tags = null;

    #[Serializer\Type(name: 'string')]
    private null|string $tagNames = null;

    #[Serializer\Type(name: 'string')]
    private null|string $description = null;
    #[Serializer\Type(name: 'int')]
    private null|int $anilistId = null;
    #[Serializer\Type(name: 'int')]
    private null|int $malId = null;

    #[Serializer\Type(name: 'string')]
    private null|string $duration = null;
    #[Serializer\Type(name: 'string')]
    private null|string $season = null;
    #[Serializer\Type(name: 'string')]
    private null|string $seasonYear = null;

    #[Serializer\Type(name: 'bool')]
    private ?bool $isAdult = null;

    #[Serializer\Type(name: 'string')]
    private ?string $countryOfOrigin = null;

    public function getBasename(): ?string
    {
        return $this->basename;
    }

    public function setBasename(?string $basename): self
    {
        $this->basename = $basename;

        return $this;
    }

    public function getGenres(): ?array
    {
        return $this->genres;
    }

    public function setGenres(?array $genres): self
    {
        $this->genres = $genres;

        return $this;
    }

    public function getTitles(): ?array
    {
        return $this->titles;
    }

    public function setTitles(?array $titles): self
    {
        $this->titles = $titles;

        return $this;
    }

    public function getAnilistId(): ?int
    {
        return $this->anilistId;
    }

    public function setAnilistId(?int $anilistId): self
    {
        $this->anilistId = $anilistId;

        return $this;
    }

    public function getMalId(): ?int
    {
        return $this->malId;
    }

    public function setMalId(?int $malId): self
    {
        $this->malId = $malId;

        return $this;
    }

    public function getDuration(): ?string
    {
        return $this->duration;
    }

    public function setDuration(?string $duration): self
    {
        $this->duration = $duration;

        return $this;
    }

    public function getSeason(): ?string
    {
        return $this->season;
    }

    public function setSeason(?string $season): self
    {
        $this->season = $season;

        return $this;
    }

    public function getSeasonYear(): ?string
    {
        return $this->seasonYear;
    }

    public function setSeasonYear(?string $seasonYear): self
    {
        $this->seasonYear = $seasonYear;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getTags(): ?array
    {
        return $this->tags;
    }

    public function setTags(?array $tags): self
    {
        $this->tags = $tags;
        if (is_array($tags)) {
            $this->tagNames = implode(', ', array_map(static fn($tag) => $tag['name'], $tags));
        }

        return $this;
    }

    public function getTagNames(): ?string
    {
        return $this->tagNames;
    }

    public function isAdult(): ?bool
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
