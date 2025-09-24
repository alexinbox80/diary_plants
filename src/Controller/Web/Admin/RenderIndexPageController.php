<?php

namespace App\Controller\Web\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class RenderIndexPageController extends AbstractController
{
    #[Route(path: '/admin', name: 'admin_render_index_page', methods: ['GET'])]
    public function __invoke(): Response
    {
        return $this->render('admin/index.html.twig');
    }
}
