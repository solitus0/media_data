<?php

declare(strict_types=1);

namespace App\Tests\Functional\AnimeData;

use App\Tests\Functional\BaseWebTestCase;

class GetFilenameControllerTest extends BaseWebTestCase
{
    public function testGetAnimeFilename(): void
    {
        $this->insertCassette('anime_filename.yml');
        $this->client->request(
            'GET',
            '/api/v1/anime/filename?rawName=One%20Piece%20-%20001%20-%20The%20Beginning%20of%20the%20Adventure%20%5B720p%5D%5BBluRay%5D%5Bx264%5D%5BAC3%5D%5BSubs%5D%5BEng%5D%5B%40Anime%20Out%20-%20www.animeout.xyz%5D.mkv'
        );

        $this->assertClientResponse(200);
        $result = $this->fetchClientContent();
        $this->assertEquals('ONE PIECE E001.mkv', $result);
    }
}
