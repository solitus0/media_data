<?php

declare(strict_types=1);

namespace App\AnimeData\Services;

use App\AnimeData\Anilist\Client\GetMediaRequest;
use App\AnimeData\Anilist\Model\DTO\Media;
use App\Shared\Cache\Enum\CacheTTL;
use App\Shared\Cache\Services\CacheManager;

class GetMediaDataUseCase
{
    public function __construct(
        private readonly GetMediaRequest $mediaRequest,
        private readonly CacheManager $cacheManager,
    ) {}

    public function get(string $title): Media
    {
        $cacheKey = sha1($title);
        $media = $this->cacheManager->get($cacheKey);
        if (!$media instanceof Media) {
            $media = $this->mediaRequest->get($title);
            $this->cacheManager->save($cacheKey, $media, CacheTTL::ANILIST);
        }

        return $media;
    }
}
