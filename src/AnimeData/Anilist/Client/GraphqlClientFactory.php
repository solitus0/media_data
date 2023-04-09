<?php

declare(strict_types=1);

namespace App\AnimeData\Anilist\Client;

use GraphQL\Client;

class GraphqlClientFactory
{
    public static function buildClient(string $url): Client
    {
        return new Client($url);
    }
}
