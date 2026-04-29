<?php

namespace App\Controller\Web\Dashboard\Security\Logout;

use LogicException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class Controller extends AbstractController
{
    #[Route('/dashboard/logout', name: 'dashboard.logout', methods: ['GET'])]
    public function logout(): void
    {
        // Этот код никогда не выполнится!
        // Symfony перехватит запрос автоматически.
        throw new LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
