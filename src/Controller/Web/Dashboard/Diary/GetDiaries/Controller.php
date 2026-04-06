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

    #[Route(
        path: '/dashboard/diaries/{year}/{month}',
        name: 'dashboard.diaries.index',
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
    public function __invoke(Request $request, ?int $year, ?int $month): Response
    {
        //перед вызовом контроллера редактирования установить переменную сессии
        $request->getSession()->set('_previous_route', $request->getRequestUri());

        $diariesModel = $this->manager->getDiaries($year, $month);

        $diaries = [
            'table_title' => $diariesModel['diaryTitle'],
            'table_type' => $diariesModel['diaryType'],
            'table_header' => $diariesModel['tableHeader'],
            'table_body' => $diariesModel['tableBody']
        ];

        return $this->render(
            'dashboard/diary/index.html.twig',
            [
                'diaries' => $diaries
            ]
        );
    }
}
