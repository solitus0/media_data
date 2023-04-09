<?php

declare(strict_types=1);

namespace App\DocApi;

class Description
{
    public const GET_FILENAME_CONTROLLER = "This endpoint attempts to parse the title, extension, year, season, and episode from a raw filename, and then calls the Anilist API to check if the parsed title exists. If the title is found, the endpoint returns the anime's English translation. </br></br>If the <code>type</code> of the rawName is <code>path</code>, the endpoint will also try to parse the season information from full path.";
    public const GET_GENERAL_FILENAME_CONTROLLER = "This endpoint attempts to parse the title, extension, year, season, and episode from a raw filename, and then calls the Anilist API to check if the parsed title exists. If the title is found, the endpoint returns the anime's English translation. </br></br>If the <code>type</code> of the rawName is <code>path</code>, the endpoint will also try to parse the season information from full path.";

}
