<?php

namespace App\Controller\Web\Dashboard\Plant\EditPlant;

use App\Domain\Entity\Plant;
use App\Domain\Service\PlantService;
use App\Controller\Form\PlantType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Controller\Web\Dashboard\Plant\EditPlant\Input\EditPlantDTO;

class Manager
{
    public function __construct(
        private readonly PlantService $plantService,
        private readonly FormFactoryInterface $formFactory
    ) {
    }

    public function editFormData(Request $request, Plant $plant): array
    {

        $formData = new EditPlantDTO(
            $plant->getTitle(),
            $plant->getRoom(),
            $plant->isShown(),
            $plant->getDescription(),
            $plant->getPurchaseDate(),
            $plant->getVaccinationDate(),
            $plant->getPlantingDate(),
            $plant->getSeller(),
            $plant->getNursery(),
            $plant->getPrice(),
            $plant->getShippingCost(),
            $plant->getPackagingCost(),
            $plant->getSoil(),
            $plant->isSold(),
            $plant->getSellingDate(),
            $plant->getSellingPrice(),
            $plant->getComment()
        );

        $form = $this->formFactory->create(PlantType::class, $formData);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var EditPlantDTO $editPlantDTO */
            $editPlantDTO = $form->getData();

            $data = $request->request->all()['plant'] ?? [];
            $editPlantDTO->isShown = (bool) ($data['isShown'] ?? false);
            $editPlantDTO->isSold = (bool) ($data['isSold'] ?? false);

            $this->plantService->updateFromEditPlantDTO($plant, $editPlantDTO);

            $request->getSession()->getFlashBag()->add('success', 'Растение успешно обновлено.');
            return ['success' => true];
        }

        return [
            'form' => $form,
            'plant' => $plant
        ];
    }
}
