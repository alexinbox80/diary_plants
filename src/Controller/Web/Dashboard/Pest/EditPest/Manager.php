<?php

namespace App\Controller\Web\Dashboard\Pest\EditPest;

use App\Domain\Entity\Pest;
use App\Controller\Form\PestType;
use App\Domain\Service\PestService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormFactoryInterface;
use App\Controller\Web\Dashboard\Pest\EditPest\Input\EditPestDTO;

class Manager
{
    public function __construct(
        private readonly PestService $pestService,
        private readonly FormFactoryInterface $formFactory
    ) {
    }

    public function editFormData(Request $request, Pest $pest): array
    {
        $formData = new EditPestDTO(
            $pest->getGroup()->getId(),
            $pest->getMarker()->getId(),
            $pest->getTitle(),
            $pest->getVolume()->getAmount(),
            $pest->getDetails()->getManufacturer(),
            $pest->getVolume()->getApplicationRate(),
            $pest->getDetails()->getDescription(),
            $pest->getDetails()->getComment()
        );

        $form = $this->formFactory->create(PestType::class, $formData, ['group_id' => 2]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var EditPestDTO $editPestDTO */
            $editPestDTO = $form->getData();

            $this->pestService->updateFromEditPestDTO($pest, $editPestDTO);

            $request->getSession()->getFlashBag()->add('success', 'Вредитель успешно обновлен.');
            return ['success' => true];
        }

        return [
            'form' => $form,
            'pest' => $pest
        ];
    }
}
