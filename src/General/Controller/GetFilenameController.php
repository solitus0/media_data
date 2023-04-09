<?php

declare(strict_types=1);

namespace App\General\Controller;

use App\DocApi\Description;
use App\General\Model\DTO\MediaName;
use App\General\Model\Query\FilenameQuery;
use App\General\Services\GetMediaNameUseCase;
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
#[OA\Tag(name: 'General')]
class GetFilenameController extends AbstractController
{
    use ProcessViolationListErrorsTrait;

    public function __construct(
        private readonly GetMediaNameUseCase $useCase,
        private readonly LoggerInterface $logger,
    ) {}

    #[Rest\Get(path: '/api/v1/general/filename')]
    #[ParamConverter(
        data: 'query',
        class: FilenameQuery::class,
        options: [
            'validator' => ['groups' => ['Default']],
        ],
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
        name: 'format',
        in: 'query',
        required: true,
        schema: new OA\Schema(ref: '#/components/schemas/FilenameQuery/properties/format'),
    )]
    #[OA\Parameter(
        name: 'folderNamePriority',
        in: 'query',
        required: false,
        schema: new OA\Schema(ref: '#/components/schemas/FilenameQuery/properties/folderNamePriority'),
    )]
    #[OA\Response(
        response: Response::HTTP_OK,
        description: Description::GET_GENERAL_FILENAME_CONTROLLER,
        content: new OA\JsonContent(ref: new Model(type: MediaName::class, groups: ['Default']))
    )]
    public function index(FilenameQuery $query, ConstraintViolationListInterface $validationErrors): MediaName
    {
        $this->logErrors($this->logger, $validationErrors);
        $this->processErrors($validationErrors);

        return $this->useCase->get($query);
    }
}
