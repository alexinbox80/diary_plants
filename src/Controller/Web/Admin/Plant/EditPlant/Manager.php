<?php

namespace App\Controller\Web\Admin\Plant\EditPlant;

use App\Controller\Web\Admin\Plant\EditPlant\Input\EditPlantDTO;
use App\Domain\Entity\Plant;
use App\Domain\Model\Plant\UpdatePlantModel;
use App\Domain\Model\Price;
use App\Domain\Service\ModelFactory;
use App\Domain\Service\PlantService;
use App\Controller\Form\PlantType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;

class Manager
{
    public function __construct(
        private readonly PlantService $plantService,
        private readonly FormFactoryInterface $formFactory,
        private readonly ModelFactory $modelFactory
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
            $plant->getComment()
        );

        $form = $this->formFactory->create(PlantType::class, $formData);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var EditPlantDTO $editPlantDTO */
            $editPlantDTO = $form->getData();

            $updatePlantModel = $this->modelFactory->makeModel(
                UpdatePlantModel::class,
                $editPlantDTO->title,
                $editPlantDTO->room,
                $editPlantDTO->isShown,
                $editPlantDTO->description,
                $editPlantDTO->plantingDate,
                $editPlantDTO->vaccinationDate,
                $editPlantDTO->plantingDate,
                $editPlantDTO->seller,
                $editPlantDTO->nursery,
                $editPlantDTO->price !== null ? Price::fromString($editPlantDTO->price) : null,
                $editPlantDTO->shippingCost !== null ? Price::fromString($editPlantDTO->shippingCost) : null,
                $editPlantDTO->packagingCost !== null ? Price::fromString($editPlantDTO->packagingCost) : null,
                $editPlantDTO->soil,
                $editPlantDTO->comment
            );

            $this->plantService->update($plant, $updatePlantModel);
        }

        return [
            'form' => $form,
            'plant' => $plant
        ];
    }
}
