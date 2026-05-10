<?php

namespace App\Controller\Web\Dashboard\Usage\CreateUsage;

use App\Controller\Form\UsageType;
use App\Domain\Service\UsageService;
use App\Application\Security\AccessContext;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use App\Controller\Web\Dashboard\Usage\CreateUsage\Input\CreateUsageDTO;


class Manager
{
    public function __construct(
        private readonly UsageService $usageService,
        private readonly FormFactoryInterface $formFactory,
        private readonly TranslatorInterface $translator,
        private readonly AccessContext $accessContext
    ) {
    }

    public function createFormData(Request $request): array
    {
        $groupId = $this->accessContext->getTargetGroupId();

        $isNew = true;

        $form = $this->formFactory->create(UsageType::class, null, ['is_new' => $isNew, 'group_id' => $groupId]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var CreateUsageDTO $createUsageDTO */
            $createUsageDTO = $form->getData();

            if (!$createUsageDTO->groupId) {
                $createUsageDTO->groupId = $groupId;
            }

            $this->usageService->createFromCreateUsageDTO($createUsageDTO);

            $message = $this->translator->trans('usage.flash.created');
            $request->getSession()->getFlashBag()->add('success', $message);
            return ['success' => true];
        }

        return [
            'form' => $form,
            'is_new' => $isNew,
        ];
    }
}
