<?php

declare(strict_types=1);

namespace App\Tests\Unit\AnimeData\Services;

use App\AnimeData\Model\Query\FilenameQuery;
use App\AnimeData\Services\ParseRawDataUseCase;
use PHPUnit\Framework\TestCase;

class ParseRawDataUseCaseTest extends TestCase
{
    private ParseRawDataUseCase $parseRawDataUseCase;

    protected function setUp(): void
    {
        $this->parseRawDataUseCase = new ParseRawDataUseCase();
    }

    /**
     * @dataProvider dataProvider
     */
    public function testParse(
        string $rawName,
        string $rawNameType,
        ?string $expectedTitle,
        ?string $expectedEpisode,
        ?string $expectedSeason,
        ?string $expectedYear,
        ?string $expectedExtension
    ): void {
        $query = (new FilenameQuery)->setRawName($rawName)->setType($rawNameType);
        $rawData = $this->parseRawDataUseCase->parse($query);
        $this->assertSame($expectedTitle, $rawData->getTitle());
        $this->assertSame($expectedEpisode, $rawData->getEpisode());
        $this->assertSame($expectedSeason, $rawData->getSeason());
        $this->assertSame($expectedYear, $rawData->getYear());
        $this->assertSame($expectedExtension, $rawData->getExtension());
    }

    public function dataProvider(): array
    {
        return [
            'dataset1' => [
                'rawName' => 'One Piece - 001 - The Birth of Luffy and His Straw Hat Crew [1080p] [Dual-Audio] [x265] [HEVC] [AAC] [10bit] [Subs] [Judas] [UTR].mkv',
                'rawNameType' => FilenameQuery::TYPE_BASENAME,
                'expectedTitle' => 'One Piece',
                'expectedEpisode' => '001',
                'expectedSeason' => null,
                'expectedYear' => null,
                'expectedExtension' => '.mkv',
            ],
            'dataset2' => [
                'rawName' => '[Subsplease] Season - 1 Akuyaku Reijou Nanode Last Boss Wo Kattemimashita - 01 (1080P) [3E953c31](1801).mkv',
                'rawNameType' => FilenameQuery::TYPE_BASENAME,
                'expectedTitle' => 'Akuyaku Reijou Nanode Last Boss Wo Kattemimashita',
                'expectedEpisode' => '01',
                'expectedSeason' => '01',
                'expectedYear' => null,
                'expectedExtension' => '.mkv',
            ],
            'dataset3' => [
                'rawName' => 'Journey Taiko Arabia Hantou De No Kiseki To Tatakai No Monogatari/Journey Taiko Arabia Hantou De No Kiseki To Tatakai No Monogatari (Shahid 1920X1080p X264 Aac).mkv',
                'rawNameType' => FilenameQuery::TYPE_BASENAME,
                'expectedTitle' => 'Journey Taiko Arabia Hantou De No Kiseki To Tatakai No Monogatari Journey Taiko Arabia Hantou De No Kiseki To Tatakai No Monogatari',
                'expectedEpisode' => null,
                'expectedSeason' => null,
                'expectedYear' => null,
                'expectedExtension' => '.mkv',
            ],
            'dataset4' => [
                'rawName' => '[SubsPlease] Mobile Suit Gundam - The Witch from Mercury - 00 (1080p) [79819B7D].mkv',
                'rawNameType' => FilenameQuery::TYPE_BASENAME,
                'expectedTitle' => 'Mobile Suit Gundam: The Witch from Mercury',
                'expectedEpisode' => '00',
                'expectedSeason' => null,
                'expectedYear' => null,
                'expectedExtension' => '.mkv',
            ],
            'dataset5' => [
                'rawName' => 'Bleach - S17E02 - 2160p WEB H.264 -NanDesuKa (B-Global) (2022).mkv',
                'rawNameType' => FilenameQuery::TYPE_BASENAME,
                'expectedTitle' => 'Bleach',
                'expectedEpisode' => '02',
                'expectedSeason' => '17',
                'expectedYear' => '2022',
                'expectedExtension' => '.mkv',
            ],
            'dataset6' => [
                'rawName' => 'Blue Lock E09.mkv',
                'rawNameType' => FilenameQuery::TYPE_BASENAME,
                'expectedTitle' => 'Blue Lock',
                'expectedEpisode' => '09',
                'expectedSeason' => null,
                'expectedYear' => null,
                'expectedExtension' => '.mkv',
            ],
            'dataset7' => [
                'rawName' => '~/Downloads/media/Shingeki no Kyojin/season 2/[Subsplease] Shingeki no Kyojin - 01 (1080P) [3E953c31].mkv',
                'rawNameType' => FilenameQuery::TYPE_PATH,
                'expectedTitle' => 'Shingeki no Kyojin',
                'expectedEpisode' => '01',
                'expectedSeason' => '02',
                'expectedYear' => null,
                'expectedExtension' => '.mkv',
            ],
            'dataset8' => [
                'rawName' => '~/Downloads/media/Shingeki no Kyojin/season/[Subsplease] Shingeki no Kyojin - 01 (1080P) [3E953c31].mkv',
                'rawNameType' => FilenameQuery::TYPE_PATH,
                'expectedTitle' => 'Shingeki no Kyojin',
                'expectedEpisode' => '01',
                'expectedSeason' => null,
                'expectedYear' => null,
                'expectedExtension' => '.mkv',
            ],
            'dataset9' => [
                'rawName' => "media/Anime series FHD/Kono Subarashii Sekai ni Shukufuku wo!/OVA/KONOSUBA -God's blessing on this wonderful world!: God's Blessings On This Wonderful Choker! E4.mkv",
                'rawNameType' => FilenameQuery::TYPE_PATH,
                'expectedTitle' => 'KONOSUBA: God s blessing on this wonderful world!: God s Blessings On This Wonderful Choker!',
                'expectedEpisode' => '04',
                'expectedSeason' => null,
                'expectedYear' => null,
                'expectedExtension' => '.mkv',
            ],
            'dataset10' => [
                'rawName' => "/media/Anime movies FHD/Isekai Quartet Movie Another World 2022/Isekai Quartet Movie Another World 2022.mkv",
                'rawNameType' => FilenameQuery::TYPE_PATH,
                'expectedTitle' => 'Isekai Quartet Movie Another World',
                'expectedEpisode' => null,
                'expectedSeason' => null,
                'expectedYear' => '2022',
                'expectedExtension' => '.mkv',
            ],
        ];
    }
}
