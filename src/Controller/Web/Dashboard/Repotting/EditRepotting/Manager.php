<?php

namespace App\Controller\Web\Dashboard\Repotting\EditRepotting;

use App\Domain\Entity\Repotting;
use App\Controller\Form\RepottingType;
use App\Domain\Service\RepottingService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormFactoryInterface;
use App\Controller\Exception\AccessDeniedException;
use Symfony\Contracts\Translation\TranslatorInterface;
use App\Application\Security\Voter\GroupOwnershipVoter;
use App\Controller\Web\Dashboard\Repotting\EditRepotting\Input\EditRepottingDTO;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

final class Manager
{
    public function __construct(
        private readonly RepottingService $repottingService,
        private readonly FormFactoryInterface $formFactory,
        private readonly TranslatorInterface $translator,
        private readonly AuthorizationCheckerInterface $authChecker
    ) {
    }

    public function editFormData(Request $request, Repotting $repotting): array
    {
        if (!$this->authChecker->isGranted(GroupOwnershipVoter::EDIT, $repotting)) {
            $message = $this->translator->trans('security.access_denied.edit');
            throw new AccessDeniedException($message);
        }

        $groupId = $repotting->getGroup()->getId();

        $formData = new EditRepottingDTO(
            $groupId,
            $repotting->getPlant()->getId(),
            $repotting->getRepottedAt(),
            $repotting->getDetails()->getType()->value,
            $repotting->getDetails()->getMaterial()->value,
            $repotting->getDetails()->getPotSize(),
            $repotting->getComment()
        );

        $form = $this->formFactory->create(RepottingType::class, $formData, ['group_id' => $groupId]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var EditRepottingDTO $editRepottingDTO */
            $editRepottingDTO = $form->getData();

            if (!$editRepottingDTO->groupId) {
                $editRepottingDTO->groupId = $groupId;
            }

            $this->repottingService->updateFromEditRepottingDTO($repotting, $editRepottingDTO);

            $message = $this->translator->trans('repotting.flash.updated', [], 'messages');
            $request->getSession()->getFlashBag()->add('success', $message);

            return ['success' => true];
        }

        return [
            'form' => $form,
            'repotting' => $repotting
        ];
    }
}
