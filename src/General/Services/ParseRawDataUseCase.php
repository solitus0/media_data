<?php

declare(strict_types=1);

namespace App\General\Services;

use App\General\Enum\MediaFormatEnum;
use App\General\Model\DTO\RawMediaData;
use App\General\Model\Query\FilenameQuery;
use App\Shared\Util\ArrayPropertyUtil;

class ParseRawDataUseCase
{
    public function parse(FilenameQuery $query): RawMediaData
    {
        $format = $query->getFormat();
        $rawData = new RawMediaData();
        $rawData->setRawName($query->getBasename());
        $rawData->setFolder($query->getFolder());
        $rawData->setFolderNamePriority($query->getFolderNamePriority());

        $baseName = preg_replace('/\[[^\]]*\]/', '', $query->getBasename());
        $baseName = preg_replace('/\((?!(19|20)\d{2})[^\)]*\)/', '', $baseName);
        $baseName = preg_replace('/(240|360|480|720|1080|2160|4320)p?/', '', $baseName);
        $baseName = preg_replace('/(full hd|hd|4k|uhd|bluray|dvd|dvdrip|hdrip|webrip|web-dl|webdl|web)/i', '', $baseName);
        $baseName = preg_replace('/(x264|x265|xvid|divx|hevc|h264|h265|h.264|h.265)/i', '', $baseName);
        $baseName = preg_replace('/(ass|srt|sub|subrip|subtitles)/i', '', $baseName);

        $baseName = preg_replace(
            '/(2\.0|5\.1|7\.1|aac|ac3|dts|dd5\.1|dd7\.1|dual audio|dual-audio|dualaudio|truehd)/i',
            '',
            $baseName
        );

        $baseName = preg_replace(
            '/(B-GLOBAL|KQRM)/i',
            '',
            $baseName
        );

        $this->setExtension($rawData, $baseName);

        if ($format === MediaFormatEnum::MOVIE) {
            $this->setYear($baseName, $rawData);
        }

        if ($format === MediaFormatEnum::SERIES) {
            $this->setSeason($baseName, $rawData);
            $season = $rawData->getSeason();
            if ($season === null) {
                $this->setSeasonFromPath($query->getRawName(), $rawData);
            }

            $this->setEpisode($baseName, $rawData);
        }

        $baseName = $this->removeExtension($baseName);
        $baseName = $this->removeFromSeasonEpisode($baseName);
        $baseName = $this->removeFromYear($baseName);
        $baseName = $this->removeNonAlphaNumericExcept($baseName);

        $baseName = preg_replace('/ -(?![ -])/', ' - ', $baseName);
        $baseName = preg_replace('/(?<!\s\w)-(?!\s\w)/', ' ', $baseName);
        $baseName = preg_replace('/\s-\s/', ': ', $baseName);
        $baseName = preg_replace('/\s+/', ' ', $baseName);
        $baseName = trim($baseName);

        $rawData->setTitle($baseName);

        return $rawData;
    }

    private function setExtension(RawMediaData $movieData, string $rawMovieName): void
    {
        $extensions = $this->getExtensions();
        $extensions = array_map(static fn($extension) => '.' . $extension, $extensions);
        $extensions = implode('|', $extensions);
        $movieFileExtensions = "/($extensions)$/i";
        $result = [];
        preg_match($movieFileExtensions, $rawMovieName, $result);
        $movieData->setExtension(ArrayPropertyUtil::getProperty($result, 0));
    }

    public function getExtensions(): array
    {
        return [
            'mkv',
            'mp4',
            'avi',
            'mov',
            'wmv',
            'flv',
            'webm',
            'vob',
            'ogv',
            'ogg',
            'drc',
            'gif',
            'gifv',
            'mng',
            'avi',
            'mov',
            'qt',
            'wmv',
            'yuv',
            'rm',
            'rmvb',
            'asf',
            'amv',
            'mp4',
            'm4p',
            'm4v',
            'mpg',
            'mp2',
            'mpeg',
            'mpe',
            'mpv',
            'm2v',
            'm4v',
            'svi',
            '3gp',
            '3g2',
            'mxf',
            'roq',
            'nsv',
            'flv',
            'f4v',
            'f4p',
            'f4a',
            'f4b',
        ];
    }

    private function setYear(string $rawMovieName, RawMediaData $movieData): void
    {
        $movieYear = '/(19[0-9][0-9]|20[0-9][0-9])/i';
        $result = [];
        preg_match($movieYear, $rawMovieName, $result);
        $movieData->setYear(ArrayPropertyUtil::getProperty($result, 0));
    }

    private function setSeason(string $rawMovieName, RawMediaData $movieInfo): void
    {
        $season = null;
        $result = [];

        preg_match('/s\d{1,2}/i', $rawMovieName, $result);
        if (!$result) {
            preg_match('/season\s{0,1}-?\s{0,1}\d{1,4}/i', $rawMovieName, $result);
        }

        if ($result) {
            $season = preg_replace('/[^0-9]/', '', ArrayPropertyUtil::getProperty($result, 0));
            $singleDigitSeason = strlen($season) === 1;
            $hasPrefix = str_starts_with($season, '0');
            if ($singleDigitSeason && !$hasPrefix) {
                $season = '0' . $season;
            }
        }

        $movieInfo->setSeason($season);
    }

    private function setSeasonFromPath(?string $getRawName, RawMediaData $rawData): void
    {
        if ($getRawName === null) {
            return;
        }

        $result = [];
        preg_match('/s\d{1,2}/i', $getRawName, $result);
        if (!$result) {
            preg_match('/season\s{0,1}-?\s{0,1}\d{1,4}/i', $getRawName, $result);
        }

        if ($result) {
            $season = preg_replace('/[^0-9]/', '', ArrayPropertyUtil::getProperty($result, 0));
            $singleDigitSeason = strlen($season) === 1;
            $hasPrefix = str_starts_with($season, '0');
            if ($singleDigitSeason && !$hasPrefix) {
                $season = '0' . $season;
            }

            $rawData->setSeason($season);
        }
    }

    private function setEpisode(string $rawMovieName, RawMediaData $movieInfo): void
    {
        $result = [];
        preg_match('/e\d{1,4}/i', $rawMovieName, $result);
        if (!$result) {
            preg_match('/(?<![s\d])\W\d{1,4}/i', $rawMovieName, $result);
        }

        $episode = ArrayPropertyUtil::getProperty($result, 0);
        if ($episode === $movieInfo->getYear()) {
            return;
        }

        if ($episode) {
            $episode = preg_replace('/[^0-9]/', '', $episode);
            $singleDigitSeason = strlen($episode) === 1;
            $hasPrefix = str_starts_with($episode, '0');
            if ($singleDigitSeason && !$hasPrefix) {
                $episode = '0' . $episode;
            }
        }

        $movieInfo->setEpisode($episode);
    }

    private function removeExtension(string $rawMovieName): string
    {
        return preg_replace('/\.[^.]*$/', '', $rawMovieName);
    }

    private function removeFromSeasonEpisode(string $rawMovieName): string
    {
        $rawMovieName = preg_replace('/season\s{0,1}-?\s{0,1}\d{1,4}/i', '', $rawMovieName);

        return preg_replace('/(s\d{1,2}e\d{1,4}|e\d{1,4}|\W\d{1,4}).*/i', '', $rawMovieName);
    }

    private function removeFromYear(string $rawMovieName): string
    {
        return preg_replace('/(19[0-9][0-9]|20[0-9][0-9]).*/i', '', $rawMovieName);
    }

    private function removeNonAlphaNumericExcept(string $rawMovieName): string
    {
        return preg_replace('/[^A-Za-z0-9\-,!:]/', ' ', $rawMovieName);
    }
}
