<?php

namespace App\Controller\Api\Dashboard\Usage\GetUsages\v1;

use OpenApi\Attributes as OA;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[AsController]
class Controller extends AbstractController
{
    public function __construct(
        private readonly Manager $manager
    ) {
    }

    #[Route(
        path: 'api/v1/dashboard/usages/{year}/{month}',
        name: 'api.v1.dashboard.usages.index',
        requirements: [
            'year' => '\d{4}',
            'month' => '0?[1-9]|1[0-2]'
        ],
        defaults: [
            'year' => null,
            'month' => null
        ],
        methods: ['GET']
    )]
    #[OA\Get(
        summary: 'Получение списка использований за конкретный период',
        tags: ['Dashboard'],
        parameters: [
            new OA\Parameter(
                name: 'year',
                description: 'Год (формат: YYYY)',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer', format: 'int32', example: 2026)
            ),
            new OA\Parameter(
                name: 'month',
                description: 'Месяц (1-12)',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer', example: 5, maximum: 12, minimum: 1)
            )
        ]
    )]
    #[OA\Response(
        response: 200,
        description: 'Список данных успешно получен',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(
                    property: 'data',
                    type: 'array',
                    items: new OA\Items(type: 'object')
                )
            ]
        )
    )]
    public function __invoke(
        ?int $year = null, ?int $month = null
    ): JsonResponse
    {
        return $this->json(['data' => $this->manager->getUsages($year, $month)]);
    }
}
