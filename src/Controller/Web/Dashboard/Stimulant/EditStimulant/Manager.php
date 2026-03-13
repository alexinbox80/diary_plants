<?php

namespace App\Controller\Web\Dashboard\Stimulant\EditStimulant;

use App\Domain\Entity\Stimulant;
use App\Controller\Form\StimulantType;
use App\Domain\Service\StimulantService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormFactoryInterface;
use App\Controller\Web\Dashboard\Stimulant\EditStimulant\Input\EditStimulantDTO;

class Manager
{
    public function __construct(
        private readonly StimulantService $stimulantService,
        private readonly FormFactoryInterface $formFactory
    ) {
    }

    public function editFormData(Request $request, Stimulant $stimulant): array
    {
        $formData = new EditStimulantDTO(
            $stimulant->getGroup()->getId(),
            $stimulant->getMarker()->getId(),
            $stimulant->getTitle(),
            $stimulant->getVolume()->getAmount(),
            $stimulant->getDetails()->getManufacturer(),
            $stimulant->getVolume()->getApplicationRate(),
            $stimulant->getDetails()->getDescription(),
            $stimulant->getDetails()->getComment()
        );

        $form = $this->formFactory->create(StimulantType::class, $formData, ['group_id' => 2]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var EditStimulantDTO $editPestDTO */
            $editStimulantDTO = $form->getData();

            $this->stimulantService->updateFromEditStimulantDTO($stimulant, $editStimulantDTO);

            $request->getSession()->getFlashBag()->add('success', 'Стимулятор успешно обновлен.');
            return ['success' => true];
        }

        return [
            'form' => $form,
            'stimulant' => $stimulant
        ];
    }
}
