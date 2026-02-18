<?php

namespace App\Controller\Web\Dashboard\Fertilizer\EditFertilizer;

use App\Domain\Entity\Fertilizer;
use App\Controller\Form\FertilizerType;
use App\Domain\Service\FertilizerService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormFactoryInterface;
use App\Controller\Web\Dashboard\Fertilizer\EditFertilizer\Input\EditFertilizerDTO;

class Manager
{
    public function __construct(
        private readonly FertilizerService $fertilizerService,
        private readonly FormFactoryInterface $formFactory
    ) {
    }

    public function editFormData(Request $request, Fertilizer $fertilizer): array
    {

        $formData = new EditFertilizerDTO(
            $fertilizer->getGroup()->getId(),
            $fertilizer->getPlant()->getId(),
            $fertilizer->getTitle(),
            $fertilizer->getQuantity(),
            $fertilizer->getLetter(),
            $fertilizer->getManufacturer(),
            $fertilizer->getDescription(),
            $fertilizer->getComment()
        );

        $form = $this->formFactory->create(FertilizerType::class, $formData);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var EditFertilizerDTO $editFertilizerDTO */
            $editFertilizerDTO = $form->getData();

            $this->fertilizerService->updateFromEditFertilizerDTO($fertilizer, $editFertilizerDTO);

            $request->getSession()->getFlashBag()->add('success', 'Удобрение успешно обновлено.');
            return ['success' => true];
        }

        return [
            'form' => $form,
            'fertilizer' => $fertilizer
        ];
    }
}
