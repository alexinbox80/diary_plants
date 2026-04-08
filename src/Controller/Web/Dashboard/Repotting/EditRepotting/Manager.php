<?php

namespace App\Controller\Web\Dashboard\Repotting\EditRepotting;

use App\Domain\Entity\Repotting;
use App\Controller\Form\RepottingType;
use App\Domain\Service\RepottingService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormFactoryInterface;
use App\Controller\Web\Dashboard\Repotting\EditRepotting\Input\EditRepottingDTO;

class Manager
{
    public function __construct(
        private readonly RepottingService $repottingService,
        private readonly FormFactoryInterface $formFactory
    ) {
    }

    public function editFormData(Request $request, Repotting $repotting): array
    {
        $formData = new EditRepottingDTO(
            $repotting->getGroup()->getId(),
            $repotting->getPlant()->getId(),
            $repotting->getRepottedAt(),
            $repotting->getDetails()->getType()->value,
            $repotting->getDetails()->getMaterial()->value,
            $repotting->getDetails()->getPotSize(),
            $repotting->getComment()
        );

        $form = $this->formFactory->create(RepottingType::class, $formData, ['group_id' => 2]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var EditRepottingDTO $editRepottingDTO */
            $editRepottingDTO = $form->getData();

            $this->repottingService->updateFromEditRepottingDTO($repotting, $editRepottingDTO);

            $request->getSession()->getFlashBag()->add('success', 'Пересадка успешно обновлена.');
            return ['success' => true];
        }

        return [
            'form' => $form,
            'repotting' => $repotting
        ];
    }
}
