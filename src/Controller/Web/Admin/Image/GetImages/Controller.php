<?php

namespace App\Controller\Web\Admin\Image\GetImages;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class Controller extends AbstractController
{
    public function __construct(
        private readonly Manager $manager
    ) {
    }

    #[Route(path: '/admin/images', name: 'admin.images.index', methods: ['GET'])]
    public function __invoke(Request $request): Response
    {
        //перед вызовом контроллера редактирования установить переменную сессии
        $request->getSession()->set('_previous_route', $request->getRequestUri());

        $plantsModel = $this->manager->getPlants();
        $plants = ['table_header' => $plantsModel['tableHeader'], 'table_body' => $plantsModel['tableBody']];

        return $this->render(
            'admin/image/index.html.twig',
            [
                'images' => $plants,
            ]
        );
    }
}
