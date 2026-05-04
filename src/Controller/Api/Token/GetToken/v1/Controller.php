<?php

namespace App\Controller\Api\Token\GetToken\v1;

use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class Controller
{
    public function __construct(
        private readonly Manager $manager
    ) {
    }

    #[Route(
        path: 'api/v1/get-token',
        name: 'api.v1.get_token',
        methods: ['POST']
    )]
    #[OA\Post(
        description: 'Метод принимает запрос и возвращает сгенерированный токен.',
        summary: 'Получение токена доступа',
        tags: ['Auth']
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ['email', 'password'],
            properties: [
                new OA\Property(property: 'email', type: 'string', example: 'mail@mail.ru'),
                new OA\Property(property: 'password', type: 'string', format: 'password', example: '12345678')
            ]
        )
    )]
    #[OA\Response(
        response: 200,
        description: 'Успешное получение токена',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'token', type: 'string', example: 'eyJhbGciOiJIUzI1...')
            ],
            type: 'object'
        )
    )]
    #[OA\Response(response: 400, description: 'Некорректные входные данные')]

    public function __invoke(Request $request): JsonResponse
    {
        $data =  ['token' => $this->manager->getToken($request)];

        return new JsonResponse($data, Response::HTTP_OK, [], false);
    }
}
