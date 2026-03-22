<?php

namespace App\Controller\Web\Dashboard\Diary\GetDiaries;

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

    #[Route(path: '/dashboard/diaries', name: 'dashboard.diaries.index', methods: ['GET'])]
    public function __invoke(Request $request): Response
    {
        //перед вызовом контроллера редактирования установить переменную сессии
        $request->getSession()->set('_previous_route', $request->getRequestUri());

        $diariesModel = $this->manager->getDiaries();
        $diaries = ['table_type' => $diariesModel['dairyType'], 'table_header' => $diariesModel['tableHeader'], 'table_body' => $diariesModel['tableBody']];

        return $this->render(
            'dashboard/diary/index.html.twig',
            [
                'diaries' => $diaries
            ]
        );
    }
}
