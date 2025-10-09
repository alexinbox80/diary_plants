<?php

namespace App\Controller\Web\Admin\Plant\EditPlant;

use App\Domain\Entity\Plant;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
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

    #[Route(path: '/admin/plants/{id}/edit', name: 'admin.plants.edit', methods: ['GET', 'POST'])]
    public function __invoke(Request $request, #[MapEntity(id: 'id')] Plant $plant): Response
    {
//        $form = $this->createForm(PlantType::class, $plant);
//        $form->handleRequest($request);

//        if ($form->isSubmitted() && $form->isValid()) {
//            $entityManager->flush();
//
//            $this->addFlash('success', 'Растение успешно обновлено.');
//
//            return $this->redirectToRoute('admin.plants.index');
//        }

        return $this->render('admin/plant/edit.html.twig',
            $this->manager->editFormData($request, $plant)
        );
    }
}
