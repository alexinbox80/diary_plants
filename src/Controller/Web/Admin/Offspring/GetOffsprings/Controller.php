<?php

namespace App\Controller\Web\Admin\Offspring\GetOffsprings;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class Controller extends AbstractController
{
    #[Route(path: '/admin/offspring', name: 'admin.offspring.index', methods: ['GET'])]
    public function __invoke(): Response
    {
        return $this->render('admin/offspring/index.html.twig');
    }
}
