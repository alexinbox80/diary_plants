<?php

namespace App\Controller\Web\Dashboard\Offspring\EditOffspring;

use App\Domain\Entity\Offspring;
use App\Controller\Form\OffspringType;
use App\Domain\Service\OffspringService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormFactoryInterface;
use App\Controller\Web\Dashboard\Offspring\EditOffspring\Input\EditOffspringDTO;

class Manager
{
    public function __construct(
        private readonly OffspringService $offspringService,
        private readonly FormFactoryInterface $formFactory
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

        $form = $this->formFactory->create(OffspringType::class, $formData, ['group_id' => 2]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var EditOffspringDTO $editOffspringDTO */
            $editOffspringDTO = $form->getData();

            $this->offspringService->updateFromEditOffspringDTO($offspring, $editOffspringDTO);

            $request->getSession()->getFlashBag()->add('success', 'Плод успешно обновлен.');
            return ['success' => true];
        }

        return [
            'form' => $form,
            'offspring' => $offspring
        ];
    }
}
