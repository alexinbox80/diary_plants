<?php

namespace App\Controller\Web\Dashboard\Plant\CreatePlant;

use App\Controller\Web\Dashboard\Plant\CreatePlant\Input\CreatePlantDTO;
use App\Domain\Entity\Plant;
use App\Domain\Model\Plant\CreatePlantModel;
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

    public function createFormData(Request $request): array
    {
        $isNew = true;

        $form = $this->formFactory->create(PlantType::class, null, ['isNew' => $isNew]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var CreatePlantDTO $createPlantDTO */
            $createPlantDTO = $form->getData();

            $createPlantModel = $this->modelFactory->makeModel(
                CreatePlantModel::class,
                $createPlantDTO->title,
                $createPlantDTO->room,
                $createPlantDTO->isShown,
                $createPlantDTO->description,
                $createPlantDTO->plantingDate,
                $createPlantDTO->vaccinationDate,
                $createPlantDTO->plantingDate,
                $createPlantDTO->seller,
                $createPlantDTO->nursery,
                $createPlantDTO->price !== null ? Price::fromString($createPlantDTO->price) : null,
                $createPlantDTO->shippingCost !== null ? Price::fromString($createPlantDTO->shippingCost) : null,
                $createPlantDTO->packagingCost !== null ? Price::fromString($createPlantDTO->packagingCost) : null,
                $createPlantDTO->soil,
                $createPlantDTO->comment
            );

            $plantModel = $this->plantService->create($createPlantModel);

            $request->getSession()->getFlashBag()->add('success', 'Растение успешно создано.');
            return ['success' => true];
        }

        return [
            'form' => $form,
            'isNew' => $isNew,
        ];
    }
}
