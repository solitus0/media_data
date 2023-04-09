<?php

declare(strict_types=1);

namespace App\Tests\Functional\AnimeData;

use App\Tests\Functional\BaseWebTestCase;

class GetFilenameControllerTest extends BaseWebTestCase
{
    public function testGetAnimeFilename(): void
    {
        $this->insertCassette('anime_filename.yml');

        $query = http_build_query(
            [
                'rawName' => '/Users/root/media_data/One Piece/Season 1/One Piece - 001 - The Beginning of the Adventure %5B720p%5D%5BBluRay%5D%5Bx264%5D%5BAC3%5D%5BSubs%5D%5BEng%5D%5B%40Anime Out - www.animeout.xyz%5D.mkv',
                'type' => 'path',
            ]
        );

        $this->client->request(
            method: 'GET',
            uri: sprintf('/api/v1/anime/filename?%s', $query),
        );

        $this->assertClientResponse(200);
        $result = $this->fetchClientContent();

        $this->assertEquals('ONE PIECE S01E001.mkv', $result['fileName']);
        $this->assertEquals('ONE PIECE/S01', $result['folderName']);
    }

    public function testGetAnimeFilename2(): void
    {
        $this->insertCassette('anime_filename.yml');

        $query = http_build_query(
            [
                'rawName' => '/Users/root/media_data/One Piece/Season 1/[SubsPlease] Mobile Suit Gundam: The Witch from Mercury - 00 (1080p) [79819B7D].mkv',
                'type' => 'path',
            ]
        );

        $this->client->request(
            method: 'GET',
            uri: sprintf('/api/v1/anime/filename?%s', $query),
        );

        $this->assertClientResponse(200);
        $result = $this->fetchClientContent();

        $this->assertEquals('Mobile Suit Gundam: The Witch from Mercury S01E00.mkv', $result['fileName']);
        $this->assertEquals('Mobile Suit Gundam: The Witch from Mercury/S01', $result['folderName']);
    }

    public function testFolderNameOverRawFilename(): void
    {
        $this->insertCassette('anime_filename.yml');

        $query = http_build_query(
            [
                'rawName' => '/Users/root/Downloads/media/season 2/BLEACH/BLEACH: Thousand-Year Blood War S17E01.mkv',
                'type' => 'path',
                'folderNamePriority' => 'true',
            ]
        );

        $this->client->request(
            method: 'GET',
            uri: sprintf('/api/v1/anime/filename?%s', $query),
        );

        $this->assertClientResponse(200);
        $result = $this->fetchClientContent();

        $this->assertEquals('Bleach S17E01.mkv', $result['fileName']);
        $this->assertEquals('Bleach/S17', $result['folderName']);
    }
}
