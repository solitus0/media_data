<?php

declare(strict_types=1);

namespace App\Tests\Functional\General;

use App\Tests\Functional\BaseWebTestCase;

class GetFilenameControllerTest extends BaseWebTestCase
{
    /**
     * @dataProvider dataProvider
     */
    public function testGetSanitized(
        string $rawName,
        string $format,
        string $expectedFileName,
        string $expectedFolderName,
        bool $folderNamePriority,
    ): void {
        $query = http_build_query(
            [
                'rawName' => $rawName,
                'format' => $format,
                'folderNamePriority' => $folderNamePriority,
            ]
        );

        $this->client->request(
            method: 'GET',
            uri: sprintf('/api/v1/general/filename?%s', $query),
        );

        $this->assertClientResponse(200);
        $result = $this->fetchClientContent();

        $this->assertEquals($expectedFileName, $result['fileName']);
        $this->assertEquals($expectedFolderName, $result['folderName']);
    }

    public function dataProvider(): array
    {
        return [
            'dataset1' => [
                'rawName' => '/Users/root/media_data/One Piece/Season 1/One Piece - 001 - The Beginning of the Adventure %5B720p%5D%5BBluRay%5D%5Bx264%5D%5BAC3%5D%5BSubs%5D%5BEng%5D%5B%40Anime Out - www.animeout.xyz%5D.mkv',
                'format' => 'series',
                'expectedFileName' => 'One Piece S01E001.mkv',
                'expectedFolderName' => 'One Piece/S01',
                'folderNamePriority' => false,
            ],
            'dataset2' => [
                'rawName' => 'Spider-Man.No.Way.Home.2021.BDRip.2160p.x265.HDR.LT.RU.EN',
                'format' => 'movie',
                'expectedFileName' => 'Spider Man No Way Home 2021',
                'expectedFolderName' => 'Spider Man No Way Home',
                'folderNamePriority' => false,
            ],
            'dataset3' => [
                'rawName' => '/Users/root/Downloads/media/season 2/BLEACH/BLEACH: Thousand-Year Blood War S17E01.mkv',
                'format' => 'series',
                'expectedFileName' => 'BLEACH S17E01.mkv',
                'expectedFolderName' => 'BLEACH/S17',
                'folderNamePriority' => true,
            ],
            'dataset4' => [
                'rawName' => 'Tensei.Shitara.Slime.Datta.Ken.Movie.Guren.no.Kizuna.hen.1080p.B-GLOBAL.WEB-DL.AAC2.0.H.264-KQRM.mkv',
                'format' => 'movie',
                'expectedFileName' => 'Tensei Shitara Slime Datta Ken Movie Guren no Kizuna hen.mkv',
                'expectedFolderName' => 'Tensei Shitara Slime Datta Ken Movie Guren no Kizuna hen',
                'folderNamePriority' => false,
            ],
            'dataset5' => [
                'rawName' => '/Users/root/media_data/One Piece/Season 1/[SubsPlease] Mobile Suit Gundam: The Witch from Mercury - 00 (1080p) [79819B7D].mkv',
                'format' => 'series',
                'expectedFileName' => 'Mobile Suit Gundam: The Witch from Mercury S01E00.mkv',
                'expectedFolderName' => 'Mobile Suit Gundam: The Witch from Mercury/S01',
                'folderNamePriority' => false,
            ],
            'dataset6' => [
                'rawName' => '/data/Anime Series FHD/Vinland Saga/S02/Vinland Saga S02E09.mkv',
                'format' => 'series',
                'expectedFileName' => 'Vinland Saga S02E09.mkv',
                'expectedFolderName' => 'Vinland Saga/S02',
                'folderNamePriority' => false,
            ],
        ];
    }
}
