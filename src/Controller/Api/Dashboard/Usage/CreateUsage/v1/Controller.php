<?php

namespace App\Controller\Api\Dashboard\Usage\CreateUsage\v1;

use OpenApi\Attributes as OA;
use Nelmio\ApiDocBundle\Attribute\Model;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Controller\Api\Dashboard\Usage\CreateUsage\v1\input\CreateUsageDTO;

class Controller extends AbstractController
{
    public function __construct(
        private readonly Manager $manager
    ) {
    }

    #[Route(
        path: 'api/v1/dashboard/usages/create',
        name: 'api.v1.dashboard.usages.create',

        methods: ['POST']
    )]
    #[OA\Post(
        summary: 'Массовое создание записей об использовании (usages)',
        tags: ['Dashboard'],
        parameters: [
            new OA\Parameter(
                name: 'X-CSRF-TOKEN',
                description: 'CSRF токен, полученный от сервера',
                in: 'header',
                required: true,
                schema: new OA\Schema(type: 'string')
            )
        ]
    )]
    #[OA\RequestBody(
        description: 'Список объектов для создания',
        required: true,
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(
                ref: new Model(type: CreateUsageDTO::class),
                example: [
                    'cellId' => 'watering-1907',
                    'plantId' => 19,
                    'date' => '2026-05-04',
                    'usableId' => 2,
                    'usableType' => 'watering'
                ]
            )
        )
    )]
    #[OA\Response(
        response: 201,
        description: 'Успешное создание',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'status', type: 'string', example: 'success'),
                new OA\Property(property: 'data', type: 'array', items: new OA\Items(type: 'object'))
            ]
        )
    )]
    #[OA\Response(response: 400, description: 'Ошибка валидации или создания')]
    public function __invoke(
        /** @var CreateUsageDTO[] $createUsagesDTO */
        #[MapRequestPayload(type: CreateUsageDTO::class)] array $createUsagesDTO
    ): JsonResponse
    {
        $results = $this->manager->createUsages($createUsagesDTO);

        if ($results) {
            return $this->json(['status' => 'success', 'data' => $results], Response::HTTP_CREATED);
        } else {
            return $this->json(['status' => 'error'], Response::HTTP_BAD_REQUEST);
        }
    }
}
