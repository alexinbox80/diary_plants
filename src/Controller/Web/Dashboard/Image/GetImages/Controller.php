<?php

namespace App\Controller\Web\Dashboard\Image\GetImages;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class Controller extends AbstractController
{
    public function __construct(
        private readonly Manager $manager
    ) {
    }

    #[Route(path: '/dashboard/images', name: 'dashboard.images.index', methods: ['GET'])]
    public function __invoke(Request $request): Response
    {
        //перед вызовом контроллера редактирования установить переменную сессии
        $request->getSession()->set('_previous_route', $request->getRequestUri());

        $attachmentsModel = $this->manager->getAttachments();
        $images = ['table_header' => $attachmentsModel['tableHeader'], 'table_body' => $attachmentsModel['tableBody']];

        return $this->render(
            'dashboard/image/index.html.twig',
            [
                'images' => $images,
            ]
        );
    }
}
