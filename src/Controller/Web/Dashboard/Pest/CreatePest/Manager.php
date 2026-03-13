<?php

namespace App\Controller\Web\Dashboard\Pest\CreatePest;

use App\Controller\Form\PestType;
use App\Domain\Service\PestService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormFactoryInterface;
use App\Controller\Web\Dashboard\Pest\CreatePest\Input\CreatePestDTO;

class Manager
{
    public function __construct(
        private readonly PestService $pestService,
        private readonly FormFactoryInterface $formFactory
    ) {
    }

    public function createFormData(Request $request): array
    {
        $isNew = true;

        $form = $this->formFactory->create(PestType::class, null, ['is_new' => $isNew, 'group_id' => 2]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var CreatePestDTO $createPestDTO */
            $createPestDTO = $form->getData();

            $this->pestService->createFromCreatePestDTO($createPestDTO);

            $request->getSession()->getFlashBag()->add('success', 'Вредитель успешно создан.');
            return ['success' => true];
        }

        return [
            'form' => $form,
            'is_new' => $isNew,
        ];
    }
}
