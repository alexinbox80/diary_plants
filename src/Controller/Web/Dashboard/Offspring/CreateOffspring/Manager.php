<?php

namespace App\Controller\Web\Dashboard\Offspring\CreateOffspring;

use App\Controller\Form\OffspringType;
use App\Domain\Service\OffspringService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormFactoryInterface;
use App\Controller\Web\Dashboard\Offspring\CreateOffspring\Input\CreateOffspringDTO;

class Manager
{
    public function __construct(
        private readonly OffspringService $offspringService,
        private readonly FormFactoryInterface $formFactory
    ) {
    }

    public function createFormData(Request $request): array
    {
        $isNew = true;

        $form = $this->formFactory->create(OffspringType::class, null, ['isNew' => $isNew]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var CreateOffspringDTO $createOffspringDTO */
            $createOffspringDTO = $form->getData();

            $this->offspringService->createFromCreateOffspringDTO($createOffspringDTO);

            $request->getSession()->getFlashBag()->add('success', 'Плод успешно создан.');
            return ['success' => true];
        }

        return [
            'form' => $form,
            'isNew' => $isNew,
        ];
    }
}
