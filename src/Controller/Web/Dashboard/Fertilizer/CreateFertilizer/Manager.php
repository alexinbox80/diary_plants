<?php

namespace App\Controller\Web\Dashboard\Fertilizer\CreateFertilizer;

use App\Controller\Form\FertilizerType;
use App\Domain\Service\FertilizerService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormFactoryInterface;
use App\Controller\Web\Dashboard\Fertilizer\CreateFertilizer\Input\CreateFertilizerDTO;

class Manager
{
    public function __construct(
        private readonly FertilizerService $fertilizerService,
        private readonly FormFactoryInterface $formFactory
    ) {
    }

    public function createFormData(Request $request): array
    {
        $isNew = true;

        $form = $this->formFactory->create(FertilizerType::class, null, ['is_new' => $isNew, 'group_id' => 2]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var CreateFertilizerDTO $createFertilizerDTO */
            $createFertilizerDTO = $form->getData();

            $this->fertilizerService->createFromCreateFertilizerDTO($createFertilizerDTO);

            $request->getSession()->getFlashBag()->add('success', 'Удобрение успешно создано.');
            return ['success' => true];
        }

        return [
            'form' => $form,
            'is_new' => $isNew,
        ];
    }
}
