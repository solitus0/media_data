<?php

declare(strict_types=1);

namespace App\General\Services;

use App\General\Model\DTO\MediaName;
use App\General\Model\DTO\RawMediaData;
use App\General\Model\Query\FilenameQuery;

class GetMediaNameUseCase
{
    public function __construct(
        private readonly ParseRawDataUseCase $rawDataUseCase,
    ) {}

    public function get(FilenameQuery $query): MediaName
    {
        $rawData = $this->rawDataUseCase->parse($query);

        return new MediaName(
            format: $query->getFormat(),
            title: $this->getTitle($query, $rawData),
            extension: $rawData->getExtension(),
            year: $rawData->getYear(),
            season: $rawData->getSeason(),
            episode: $rawData->getEpisode()
        );
    }

    private function getTitle(FilenameQuery $query, RawMediaData $rawData): string
    {
        $title = $rawData->getTitle();
        if ($query->getFolderNamePriority() && $query->getFolder() !== null) {
            $title = $query->getFolder();
        }

        return $title;
    }
}
