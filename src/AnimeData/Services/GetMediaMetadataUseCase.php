<?php

declare(strict_types=1);

namespace App\AnimeData\Services;

use App\AnimeData\Anilist\Model\DTO\Media;
use App\AnimeData\Model\DTO\MediaName;
use App\AnimeData\Model\DTO\Metadata;
use App\AnimeData\Model\DTO\RawMediaData;
use App\AnimeData\Model\Query\FilenameQuery;
use GuzzleHttp\Exception\BadResponseException;

class GetMediaMetadataUseCase
{
    public function __construct(
        private readonly ParseRawDataUseCase $rawDataUseCase,
        private readonly GetDataFromFileQueryUseCase $queryUseCase,
    ) {}

    public function get(FilenameQuery $query): Metadata
    {
        $rawData = $this->rawDataUseCase->parse($query);
        $mediaData = $this->queryUseCase->get($rawData);

        return $this->buildMetadata($mediaData, $rawData);
    }

    private function buildMetadata(Media $media, RawMediaData $rawData): Metadata
    {
        $result = new Metadata();
        $result->setBasename($this->getBasename($media, $rawData));
        $result->setGenres($media->getGenres());
        $result->setTitles($media->getTitles());
        $result->setAnilistId($media->getId());
        $result->setMalId($media->getIdMal());
        $result->setDuration($media->getDuration());
        $result->setSeason($media->getSeason());
        $result->setSeasonYear($media->getSeasonYear());
        $result->setTags($media->getTags());
        $result->setIsAdult($media->getIsAdult());
        $result->setCountryOfOrigin($media->getCountryOfOrigin());
        $description = $media->getDescription();
        $description = preg_replace('/\s+/', ' ', $description);
        $description = preg_replace('/<[^>]*>/', '', $description);

        $result->setDescription($description);

        return $result;
    }

    private function getBasename(Media $media, RawMediaData $rawData): string
    {
        $result = new MediaName(
            format: $media->getFormat(),
            title: $media->getTitle(),
            extension: $rawData->getExtension(),
            year: $media->getSeasonYear(),
            season: $rawData->getSeason(),
            episode: $rawData->getEpisode()
        );

        return $result->getBasename();
    }
}
