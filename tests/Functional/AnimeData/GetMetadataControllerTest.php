<?php

declare(strict_types=1);

namespace App\Tests\Functional\AnimeData;

use App\Tests\Functional\BaseWebTestCase;

class GetMetadataControllerTest extends BaseWebTestCase
{
    public function testAnimeMetadata(): void
    {
        $this->insertCassette('anime_metadata.yml');
        $this->client->request(
            'GET',
            '/api/v1/anime/metadata?rawName=One%20Piece%20-%20001%20-%20The%20Beginning%20of%20the%20Adventure%20%5B720p%5D%5BBluRay%5D%5Bx264%5D%5BAC3%5D%5BSubs%5D%5BEng%5D%5B%40Anime%20Out%20-%20www.animeout.xyz%5D.mkv'
        );

        $this->assertClientResponse(200);
        $result = $this->fetchClientContent();
        $this->assertEquals('ONE PIECE E001', $result['basename']);
        $this->assertEquals(['Action', 'Adventure', 'Comedy', 'Drama', 'Fantasy'], $result['genres']);
        $this->assertEquals(
            [
                'romaji' => 'ONE PIECE',
                'english' => 'ONE PIECE',
                'native' => 'ONE PIECE',
            ],
            $result['titles']
        );
    }
}
