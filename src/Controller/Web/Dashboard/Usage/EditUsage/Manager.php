<?php

namespace App\Controller\Web\Dashboard\Usage\EditUsage;

use App\Domain\Entity\Usage;
use App\Controller\Form\UsageType;
use App\Domain\Service\UsageService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormFactoryInterface;
use App\Controller\Web\Dashboard\Usage\EditUsage\Input\EditUsageDTO;

class Manager
{
    public function __construct(
        private readonly UsageService $usageService,
        private readonly FormFactoryInterface $formFactory
    ) {
    }

    public function editFormData(Request $request, Usage $usage): array
    {
        $formData = new EditUsageDTO(
            $usage->getGroup()->getId(),
            $usage->getPlant()->getId(),
            $usage->getUseDate(),
            $usage->getTarget()->getUsableId(),
            $usage->getTarget()->getUsableType()->value,
            $usage->getComment()
        );

        $form = $this->formFactory->create(UsageType::class, $formData, ['group_id' => 2]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var EditUsageDTO $editUsageDTO */
            $editUsageDTO = $form->getData();

            $this->usageService->updateFromEditUsageDTO($usage, $editUsageDTO);

            $request->getSession()->getFlashBag()->add('success', 'Использование успешно обновлено.');
            return ['success' => true];
        }

        return [
            'form' => $form,
            'usage' => $usage
        ];
    }
}
