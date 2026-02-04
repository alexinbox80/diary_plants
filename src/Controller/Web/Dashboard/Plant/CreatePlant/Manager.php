<?php

namespace App\Controller\Web\Dashboard\Plant\CreatePlant;

use App\Controller\Form\PlantType;
use App\Domain\Service\PlantService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormFactoryInterface;
use App\Controller\Web\Dashboard\Plant\CreatePlant\Input\CreatePlantDTO;

class Manager
{
    public function __construct(
        private readonly PlantService $plantService,
        private readonly FormFactoryInterface $formFactory
    ) {
    }

    public function createFormData(Request $request): array
    {
        $isNew = true;

        $form = $this->formFactory->create(PlantType::class, null, ['isNew' => $isNew]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var CreatePlantDTO $createPlantDTO */
            $createPlantDTO = $form->getData();

            $plantModel = $this->plantService->createFromCreatePlantDTO($createPlantDTO);

            $request->getSession()->getFlashBag()->add('success', 'Растение успешно создано.');
            return ['success' => true];
        }

        return [
            'form' => $form,
            'isNew' => $isNew,
        ];
    }
}
