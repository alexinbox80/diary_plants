<?php

namespace App\Controller\Web\Dashboard\Watering\EditWatering;

use App\Domain\Entity\Watering;
use App\Controller\Form\WateringType;
use App\Domain\Service\WateringService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormFactoryInterface;
use App\Controller\Web\Dashboard\Watering\EditWatering\Input\EditWateringDTO;

class Manager
{
    public function __construct(
        private readonly WateringService $wateringService,
        private readonly FormFactoryInterface $formFactory
    ) {
    }

    public function editFormData(Request $request, Watering $watering): array
    {
        $formData = new EditWateringDTO(
            $watering->getGroup()->getId(),
            $watering->getMarker()->getId(),
            $watering->getDetails()->getAmount(),
            $watering->getDetails()->getMethod()->value,
            $watering->getDetails()->getType()->value,
            $watering->getDetails()->getTemperature(),
            $watering->getDescription(),
            $watering->getComment()
        );

        $form = $this->formFactory->create(WateringType::class, $formData, ['group_id' => 2]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var EditWateringDTO $editWateringDTO */
            $editWateringDTO = $form->getData();

            $this->wateringService->updateFromEditWateringDTO($watering, $editWateringDTO);

            $request->getSession()->getFlashBag()->add('success', 'Полив успешно обновлен.');
            return ['success' => true];
        }

        return [
            'form' => $form,
            'watering' => $watering
        ];
    }
}
