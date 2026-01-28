<?php

namespace App\Controller\Web\Dashboard\Offspring\EditOffspring;

use App\Controller\Web\Dashboard\Offspring\EditOffspring\Input\EditOffspringDTO;
use App\Domain\Entity\Offspring;
use App\Domain\Model\Offspring\UpdateOffspringModel;
use App\Domain\Service\ModelFactory;
use App\Domain\Service\OffspringService;
use App\Controller\Form\OffspringType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;

class Manager
{
    public function __construct(
        private readonly OffspringService $offspringService,
        private readonly FormFactoryInterface $formFactory,
        private readonly ModelFactory $modelFactory
    ) {
    }

    public function editFormData(Request $request, Offspring $offspring): array
    {

        $formData = new EditOffspringDTO(
            $offspring->getPlant()->getId(),
            $offspring->getFruitingDate(),
            $offspring->getFloweringDate(),
            $offspring->getMass(),
            $offspring->getColor(),
            $offspring->getFlavor(),
            $offspring->getQuantity(),
            $offspring->getComment()
        );

        $form = $this->formFactory->create(OffspringType::class, $formData);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var EditOffspringDTO $editOffspringDTO */
            $editOffspringDTO = $form->getData();

            $updateOffspringModel = $this->modelFactory->makeModel(
                UpdateOffspringModel::class,
                $editOffspringDTO->plantId,
                $editOffspringDTO->fruitingDate,
                $editOffspringDTO->floweringDate,
                $editOffspringDTO->mass,
                $editOffspringDTO->color,
                $editOffspringDTO->flavor,
                $editOffspringDTO->quantity,
                $editOffspringDTO->comment
            );

            $this->offspringService->update($offspring, $updateOffspringModel);

            $request->getSession()->getFlashBag()->add('success', 'Плод успешно обновлен.');
            return ['success' => true];
        }

        return [
            'form' => $form,
            'offspring' => $offspring
        ];
    }
}
