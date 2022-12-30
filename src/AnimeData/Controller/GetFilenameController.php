<?php

declare(strict_types=1);

namespace App\AnimeData\Controller;

use App\AnimeData\Model\Query\FilenameQuery;
use App\AnimeData\Services\GetMediaNameUseCase;
use App\DocApi\Description;
use App\Shared\Exception\Trait\ProcessViolationListErrorsTrait;
use FOS\RestBundle\Controller\Annotations as Rest;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Validator\ConstraintViolationListInterface;

#[AsController]
#[OA\Tag(name: 'Anime')]
class GetFilenameController extends AbstractController
{
    use ProcessViolationListErrorsTrait;

    public function __construct(private readonly GetMediaNameUseCase $useCase) {}

    #[Rest\Get(path: '/api/v1/anime/filename')]
    #[ParamConverter(
        data: 'query',
        class: FilenameQuery::class,
        options: ['validator' => ['groups' => ['Default']]],
        converter: 'app.query'
    )]
    #[OA\Schema(ref: new Model(type: FilenameQuery::class))]
    #[OA\Parameter(
        name: 'rawName',
        in: 'query',
        required: true,
        schema: new OA\Schema(ref: '#/components/schemas/FilenameQuery/properties/rawName'),
        examples: [
            new OA\Examples(
                example: 'Example 1',
                summary: 'Filename with resolution, subs and rip data.',
                value: '[Subsplease] Shingeki no Kyojin - 01 (1080P) [3E953c31].mkv',
            ),
        ]
    )]
    #[OA\Parameter(
        name: 'type',
        in: 'query',
        required: true,
        schema: new OA\Schema(ref: '#/components/schemas/FilenameQuery/properties/type'),
    )]
    #[OA\Response(
        response: Response::HTTP_OK,
        description: Description::GET_FILENAME_CONTROLLER,
        content: new OA\JsonContent(
            schema: 'string',
            title: 'Filename',
            example: 'Attack on Titan S01E01.mkv'
        )
    )]
    #[OA\Response(response: Response::HTTP_NOT_FOUND, description: 'Anilist API returned no results.')]
    public function index(FilenameQuery $query, ConstraintViolationListInterface $validationErrors): string
    {
        $this->processErrors($validationErrors);

        return $this->useCase->get($query);
    }
}
