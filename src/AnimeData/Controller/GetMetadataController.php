<?php

declare(strict_types=1);

namespace App\AnimeData\Controller;

use App\AnimeData\Model\DTO\Metadata;
use App\AnimeData\Model\Query\FilenameQuery;
use App\AnimeData\Services\GetMediaMetadataUseCase;
use App\Shared\Exception\Trait\ProcessViolationListErrorsTrait;
use FOS\RestBundle\Controller\Annotations as Rest;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Validator\ConstraintViolationListInterface;

#[AsController]
#[OA\Tag(name: 'Anime')]
class GetMetadataController extends AbstractController
{
    use ProcessViolationListErrorsTrait;

    public function __construct(
        private readonly GetMediaMetadataUseCase $useCase,
        private readonly LoggerInterface $logger,
    ) {}

    #[ParamConverter(
        data: 'query',
        class: FilenameQuery::class,
        options: ['validator' => ['groups' => ['Default']]],
        converter: 'app.query'
    )]
    #[Rest\Get(path: '/api/v1/anime/metadata')]
    #[OA\Parameter(
        name: 'rawName',
        in: 'query',
        required: true,
        schema: new OA\Schema(type: 'string'),
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
        required: false,
        schema: new OA\Schema(ref: '#/components/schemas/FilenameQuery/properties/type'),
    )]
    #[OA\Parameter(
        name: 'folderNamePriority',
        in: 'query',
        required: false,
        schema: new OA\Schema(ref: '#/components/schemas/FilenameQuery/properties/folderNamePriority'),
    )]
    #[OA\Response(
        response: Response::HTTP_OK,
        description: 'Returns metadata from Anilist API.',
        content: new OA\JsonContent(ref: new Model(type: Metadata::class, groups: ['Default']))
    )]
    #[OA\Response(response: Response::HTTP_NOT_FOUND, description: 'Anilist API returned no results.')]
    public function index(FilenameQuery $query, ConstraintViolationListInterface $validationErrors): Metadata
    {
        $this->logErrors($this->logger, $validationErrors);
        $this->processErrors($validationErrors);

        return $this->useCase->get($query);
    }
}
