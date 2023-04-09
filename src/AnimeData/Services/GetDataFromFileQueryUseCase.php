<?php

declare(strict_types=1);

namespace App\AnimeData\Services;

use App\AnimeData\Anilist\Model\DTO\Media;
use App\AnimeData\Model\DTO\RawMediaData;
use GuzzleHttp\Exception\BadResponseException;

class GetDataFromFileQueryUseCase
{
    public function __construct(
        private readonly GetMediaDataUseCase $mediaDataUseCase
    ) {}

    public function get(RawMediaData $rawData): Media
    {
        try {
            if ($rawData->shouldPrioritiseFolderName()) {
                $title = $rawData->getFolder();
            } else {
                $title = $rawData->getTitle();
            }

            $mediaData = $this->mediaDataUseCase->get($title);
        } catch (BadResponseException $e) {
            if ($e->getCode() !== 404) {
                throw $e;
            }

            if ($rawData->shouldPrioritiseFolderName()) {
                $title = $rawData->getTitle();
            }

            if (!$rawData->shouldPrioritiseFolderName() && $rawData->getFolder()) {
                $title = $rawData->getFolder();
            }

            if (!isset($title)) {
                throw $e;
            }

            $mediaData = $this->mediaDataUseCase->get($title);
        }

        return $mediaData;
    }
}
