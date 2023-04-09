<?php

declare(strict_types=1);

namespace App\General\Model\DTO;

use JMS\Serializer\Annotation as Serializer;

class RawMediaData
{
    #[Serializer\Type(name: 'string')]
    private ?string $rawName = null;

    #[Serializer\Type(name: 'string')]
    private ?string $extension = null;

    #[Serializer\Type(name: 'string')]
    private ?string $year = null;

    #[Serializer\Type(name: 'string')]
    private ?string $season = null;

    #[Serializer\Type(name: 'string')]
    private ?string $episode = null;

    #[Serializer\Type(name: 'string')]
    private ?string $title = null;

    #[Serializer\Type(name: 'string')]
    private ?string $folder = null;

    #[Serializer\Type(name: 'bool')]
    private ?bool $folderNamePriority = null;

    public function getRawName(): ?string
    {
        return $this->rawName;
    }

    public function setRawName(?string $rawName): self
    {
        $this->rawName = $rawName;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getExtension(): ?string
    {
        return $this->extension;
    }

    public function setExtension(?string $extension): self
    {
        $this->extension = $extension;

        return $this;
    }

    public function getYear(): ?string
    {
        return $this->year;
    }

    public function setYear(?string $year): self
    {
        $this->year = $year;

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

    public function getEpisode(): ?string
    {
        if ($this->episode && preg_match('/[a-zA-Z]/', $this->episode)) {
            $this->episode = preg_replace('/[a-zA-Z]/', '', $this->episode);
        }

        return $this->episode;
    }

    public function setEpisode(?string $episode): self
    {
        $this->episode = $episode;

        return $this;
    }

    public function getFolder(): ?string
    {
        return $this->folder;
    }

    public function setFolder(?string $folder): self
    {
        $this->folder = $folder;

        return $this;
    }

    public function isFolderNamePriority(): ?bool
    {
        return $this->folderNamePriority;
    }

    public function setFolderNamePriority(?bool $folderNamePriority): void
    {
        $this->folderNamePriority = $folderNamePriority;
    }

    public function shouldPrioritiseFolderName(): bool
    {
        return $this->isFolderNamePriority() && $this->getFolder();
    }
}
