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
        private readonly GetDataFromFileQueryUseCase $queryUseCase,
    ) {}

    public function get(FilenameQuery $query): MediaName
    {
        $rawData = $this->rawDataUseCase->parse($query);
        $mediaData = $this->queryUseCase->get($rawData);

        return $this->getFilename($mediaData, $rawData);
    }

    private function getFilename(Media $media, RawMediaData $rawData): MediaName
    {
        return new MediaName(
            format: $media->getFormat(),
            title: $media->getTitle(),
            extension: $rawData->getExtension(),
            year: $media->getSeasonYear(),
            season: $rawData->getSeason(),
            episode: $rawData->getEpisode()
        );
    }
}
