<?php

namespace App\Controller\Api\Dashboard\Usage\DeleteUsage\v1;

use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class Controller extends AbstractController
{
    public function __construct(
        private readonly Manager $manager
    ) {
    }

    #[Route(path: 'api/v1/dashboard/usages/remove', name: 'api.v1.dashboard.usages.delete', methods: ['DELETE'])]
    #[OA\Delete(
        summary: 'Массовое удаление записей',
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
        required: true,
        content: new OA\JsonContent(
            properties: [
                new OA\Property(
                    property: 'ids',
                    type: 'array',
                    items: new OA\Items(type: 'integer'),
                    example: [1, 5, 12]
                )
            ]
        )
    )]
    #[OA\Response(
        response: 200,
        description: 'Успешное удаление',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'status', type: 'string', example: 'success'),
                new OA\Property(property: 'total', description: 'Количество удаленных записей', type: 'integer', example: 3)
            ]
        )
    )]
    #[OA\Response(
        response: 400,
        description: 'Пустой список ID'
    )]
    public function __invoke(Request $request): Response
    {
        $data = json_decode($request->getContent(), true);
        $ids = $data['ids'] ?? [];

        if (empty($ids)) {
            return $this->json(['status' => 'error', 'message' => 'IDs are required'], Response::HTTP_BAD_REQUEST);
        }

        try {
            $total = $this->manager->removeByIds($ids);
            return $this->json(['status' => 'success', 'total' => $total], Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->json(['status' => 'error', 'message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
