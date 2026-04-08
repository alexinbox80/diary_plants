<?php

namespace App\Controller\Web\Dashboard\Repotting\CreateRepotting;

use App\Controller\Form\RepottingType;
use App\Domain\Service\RepottingService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormFactoryInterface;
use App\Controller\Web\Dashboard\Repotting\CreateRepotting\Input\CreateRepottingDTO;

class Manager
{
    public function __construct(
        private readonly RepottingService $repottingService,
        private readonly FormFactoryInterface $formFactory
    ) {
    }

    public function createFormData(Request $request): array
    {
        $isNew = true;

        $form = $this->formFactory->create(RepottingType::class, null, ['is_new' => $isNew, 'group_id' => 2]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var CreateRepottingDTO $createRepottingDTO */
            $createRepottingDTO = $form->getData();

            $this->repottingService->createFromCreateRepottingDTO($createRepottingDTO);

            $request->getSession()->getFlashBag()->add('success', 'Пересадка создана.');
            return ['success' => true];
        }

        return [
            'form' => $form,
            'is_new' => $isNew,
        ];
    }
}
