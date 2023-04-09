<?php

declare(strict_types=1);

namespace App\AnimeData\Anilist\Client;

use App\AnimeData\Anilist\Model\DTO\Media;
use App\Shared\Util\ArrayPropertyUtil;
use GraphQL\Client;
use GraphQL\Query;
use GraphQL\RawObject;
use GraphQL\Results;
use JMS\Serializer\SerializerInterface;

class GetMediaRequest
{
    public function __construct(
        private readonly Client $client,
        private readonly SerializerInterface $serializer,
    ) {}

    public function get(string $title): Media
    {
        $mediaQql = (new Query('Media'))
            ->setSelectionSet(
                [
                    (new Query('id')),
                    (new Query('idMal')),
                    (new Query('format')),
                    (new Query('type')),
                    (new Query('genres')),
                    (new Query('description')),
                    (new Query('episodes')),
                    (new Query('seasonYear')),
                    (new Query('duration')),
                    (new Query('isAdult')),
                    (new Query('countryOfOrigin')),
                    (new Query('tags'))
                        ->setSelectionSet(
                            [
                                (new Query('name')),
                                (new Query('rank')),
                            ]
                        ),
                    (new Query('title'))
                        ->setSelectionSet(
                            [
                                'romaji',
                                'english',
                                'native',
                            ]
                        ),
                ]
            )
        ;

        $mediaQql->setArguments(
            [
                'search' => $title,
                'type' => new RawObject('ANIME'),
            ]
        );

        $results = $this->client->runQuery($mediaQql);

        return $this->deserializeResult($results);
    }

    private function deserializeResult(Results $results): Media
    {
        $results->reformatResults(true);
        $data = $results->getResults();
        $data = ArrayPropertyUtil::getProperty($data, 'data');
        $media = ArrayPropertyUtil::getProperty($data, 'Media');
        $content = json_encode($media);

        return $this->serializer->deserialize($content, Media::class, 'json');
    }
}
