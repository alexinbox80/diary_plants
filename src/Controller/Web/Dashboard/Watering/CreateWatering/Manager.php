<?php

namespace App\Controller\Web\Dashboard\Watering\CreateWatering;

use App\Controller\Form\WateringType;
use App\Domain\Service\WateringService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormFactoryInterface;
use App\Controller\Web\Dashboard\Watering\CreateWatering\Input\CreateWateringDTO;

class Manager
{
    public function __construct(
        private readonly WateringService $wateringService,
        private readonly FormFactoryInterface $formFactory
    ) {
    }

    public function createFormData(Request $request): array
    {
        $isNew = true;

        $form = $this->formFactory->create(WateringType::class, null, ['is_new' => $isNew, 'group_id' => 2]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var CreateWateringDTO $createWateringDTO */
            $createWateringDTO = $form->getData();

            $this->wateringService->createFromCreateWateringDTO($createWateringDTO);

            $request->getSession()->getFlashBag()->add('success', 'Полив успешно создан.');
            return ['success' => true];
        }

        return [
            'form' => $form,
            'is_new' => $isNew,
        ];
    }
}
