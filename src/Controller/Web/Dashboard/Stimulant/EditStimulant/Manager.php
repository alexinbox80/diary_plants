<?php

namespace App\Controller\Web\Dashboard\Stimulant\EditStimulant;

use App\Domain\Entity\Stimulant;
use App\Controller\Form\StimulantType;
use App\Domain\Service\StimulantService;
use Symfony\Component\HttpFoundation\Request;
use App\Domain\Exception\AccessDeniedException;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use App\Application\Security\Voter\GroupOwnershipVoter;
use App\Controller\Web\Dashboard\Stimulant\EditStimulant\Input\EditStimulantDTO;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

final class Manager
{
    public function __construct(
        private readonly StimulantService $stimulantService,
        private readonly FormFactoryInterface $formFactory,
        private readonly TranslatorInterface $translator,
        private readonly AuthorizationCheckerInterface $authChecker
    ) {
    }

    public function editFormData(Request $request, Stimulant $stimulant): array
    {
        if (!$this->authChecker->isGranted(GroupOwnershipVoter::EDIT, $stimulant)) {
            $message = $this->translator->trans('security.access_denied.edit');
            throw new AccessDeniedException($message);
        }

        $groupId = $stimulant->getGroup()->getId();

        $formData = new EditStimulantDTO(
            groupId: $groupId,
            markerId: $stimulant->getMarker()->getId(),
            title: $stimulant->getTitle(),
            amount: $stimulant->getVolume()->getAmount(),
            manufacturer: $stimulant->getDetails()->getManufacturer(),
            applicationRate: $stimulant->getVolume()->getApplicationRate(),
            description: $stimulant->getDetails()->getDescription(),
            comment: $stimulant->getDetails()->getComment()
        );

        $form = $this->formFactory->create(StimulantType::class, $formData, ['group_id' => $groupId]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var EditStimulantDTO $editStimulantDTO */
            $editStimulantDTO = $form->getData();

            if (!$editStimulantDTO->groupId) {
                $editStimulantDTO->groupId = $groupId;
            }

            $this->stimulantService->updateFromEditStimulantDTO($stimulant, $editStimulantDTO);

            $message = $this->translator->trans('stimulant.flash.updated', [], 'messages');
            $request->getSession()->getFlashBag()->add('success', $message);
            return ['success' => true];
        }

        return [
            'form' => $form,
            'stimulant' => $stimulant
        ];
    }
}
