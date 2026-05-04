<?php

namespace App\Controller\Api\Token\RefreshToken\v1;

use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class Controller extends AbstractController
{
    public function __construct(
        private readonly Manager $manager
    ) {
    }

    #[Route(
        path: 'api/v1/refresh-token',
        name: 'api.v1.refresh_token',
        methods: ['POST']
    )]
    #[OA\Post(
        description: 'Метод берет текущего авторизованного пользователя и генерирует для него новый токен. Полученный токен можно расшифровать на [jwt.io](https://jwt.io)',
        summary: 'Обновление токена доступа',
        security: [
            ['Bearer' => []]
        ],
        tags: ['Auth'],
    )]
    #[OA\Response(
        response: 200,
        description: 'Токен успешно обновлен',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'token', type: 'string', example: 'new.jwt.token.here')
            ]
        )
    )]
    #[OA\Response(
        response: 401,
        description: 'Пользователь не авторизован'
    )]
    public function __invoke(Request $request): JsonResponse
    {
        $data = ['token' => $this->manager->refreshToken($this->getUser())];

        return new JsonResponse($data, Response::HTTP_OK, [], false);
    }
}
