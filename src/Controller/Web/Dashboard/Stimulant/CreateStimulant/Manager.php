<?php

namespace App\Controller\Web\Dashboard\Stimulant\CreateStimulant;

use App\Controller\Form\StimulantType;
use App\Domain\Service\StimulantService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormFactoryInterface;
use App\Controller\Web\Dashboard\Stimulant\CreateStimulant\Input\CreateStimulantDTO;

class Manager
{
    public function __construct(
        private readonly StimulantService $stimulantService,
        private readonly FormFactoryInterface $formFactory
    ) {
    }

    public function createFormData(Request $request): array
    {
        $isNew = true;

        $form = $this->formFactory->create(StimulantType::class, null, ['is_new' => $isNew, 'group_id' => 2]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var CreateStimulantDTO $createStimulantDTO */
            $createStimulantDTO = $form->getData();

            $this->stimulantService->createFromCreateStimulantDTO($createStimulantDTO);

            $request->getSession()->getFlashBag()->add('success', 'Стимулятор успешно создан.');
            return ['success' => true];
        }

        return [
            'form' => $form,
            'is_new' => $isNew,
        ];
    }
}
