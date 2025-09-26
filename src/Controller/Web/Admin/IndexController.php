<?php

namespace App\Controller\Web\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class IndexController extends AbstractController
{
    #[Route(path: '/admin', name: 'admin.index', methods: ['GET'])]
    public function __invoke(): Response
    {
        return $this->render('admin/index.html.twig');
    }
}
