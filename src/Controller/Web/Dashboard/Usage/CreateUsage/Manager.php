<?php

namespace App\Controller\Web\Dashboard\Usage\CreateUsage;

use App\Controller\Form\UsageType;
use App\Domain\Service\UsageService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormFactoryInterface;
use App\Controller\Web\Dashboard\Usage\CreateUsage\Input\CreateUsageDTO;

class Manager
{
    public function __construct(
        private readonly UsageService $usageService,
        private readonly FormFactoryInterface $formFactory
    ) {
    }

    public function createFormData(Request $request): array
    {
        $isNew = true;

        $form = $this->formFactory->create(UsageType::class, null, ['is_new' => $isNew, 'group_id' => 2]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var CreateUsageDTO $createUsageDTO */
            $createUsageDTO = $form->getData();

            $this->usageService->createFromCreateUsageDTO($createUsageDTO);

            $request->getSession()->getFlashBag()->add('success', 'Использование успешно создано.');
            return ['success' => true];
        }

        return [
            'form' => $form,
            'is_new' => $isNew,
        ];
    }
}
