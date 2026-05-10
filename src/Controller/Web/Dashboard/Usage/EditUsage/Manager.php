<?php

namespace App\Controller\Web\Dashboard\Usage\EditUsage;

use App\Domain\Entity\Usage;
use App\Controller\Form\UsageType;
use App\Domain\Service\UsageService;
use Symfony\Component\HttpFoundation\Request;
use App\Domain\Exception\AccessDeniedException;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use App\Application\Security\Voter\GroupOwnershipVoter;
use App\Controller\Web\Dashboard\Usage\EditUsage\Input\EditUsageDTO;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class Manager
{
    public function __construct(
        private readonly UsageService $usageService,
        private readonly FormFactoryInterface $formFactory,
        private readonly TranslatorInterface $translator,
        private readonly AuthorizationCheckerInterface $authChecker
    ) {
    }

    public function editFormData(Request $request, Usage $usage): array
    {
        if (!$this->authChecker->isGranted(GroupOwnershipVoter::EDIT, $usage)) {
            $message = $this->translator->trans('security.access_denied.edit');
            throw new AccessDeniedException($message);
        }

        $groupId = $usage->getGroup()->getId();

        $formData = new EditUsageDTO(
            $groupId,
            $usage->getPlant()->getId(),
            $usage->getUseDate(),
            $usage->getTarget()->getUsableId(),
            $usage->getTarget()->getUsableType()->value,
            $usage->getComment()
        );

        $form = $this->formFactory->create(UsageType::class, $formData, ['group_id' => $groupId]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var EditUsageDTO $editUsageDTO */
            $editUsageDTO = $form->getData();

            if (!$editUsageDTO->groupId) {
                $editUsageDTO->groupId = $groupId;
            }

            $this->usageService->updateFromEditUsageDTO($usage, $editUsageDTO);

            $message = $this->translator->trans('usage.flash.updated', [], 'messages');
            $request->getSession()->getFlashBag()->add('success', $message);
            return ['success' => true];
        }

        return [
            'form' => $form,
            'usage' => $usage
        ];
    }
}
