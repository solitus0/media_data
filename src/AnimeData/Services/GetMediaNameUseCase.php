<?php

declare(strict_types=1);

namespace App\AnimeData\Services;

use App\AnimeData\Anilist\Model\DTO\Media;
use App\AnimeData\Model\DTO\MediaName;
use App\AnimeData\Model\DTO\RawMediaData;
use App\AnimeData\Model\Query\FilenameQuery;

class GetMediaNameUseCase
{
    public function __construct(
        private readonly ParseRawDataUseCase $rawDataUseCase,
        private readonly GetMediaDataUseCase $mediaDataUseCase
    ) {}

    public function get(FilenameQuery $query): string
    {
        $rawData = $this->rawDataUseCase->parse($query);
        $mediaData = $this->mediaDataUseCase->get($rawData->getTitle());

        return $this->getFilename($mediaData, $rawData);
    }

    private function getFilename(Media $media, RawMediaData $rawData): string
    {
        $result = new MediaName(
            format: $media->getFormat(),
            title: $media->getTitle(),
            extension: $rawData->getExtension(),
            year: $media->getSeasonYear(),
            season: $rawData->getSeason(),
            episode: $rawData->getEpisode()
        );

        return $result->getFilename();
    }
}
