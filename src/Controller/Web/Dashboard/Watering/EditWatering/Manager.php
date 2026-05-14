<?php

namespace App\Controller\Web\Dashboard\Watering\EditWatering;

use App\Domain\Entity\Watering;
use App\Controller\Form\WateringType;
use App\Domain\Service\WateringService;
use Symfony\Component\HttpFoundation\Request;
use App\Domain\Exception\AccessDeniedException;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use App\Application\Security\Voter\GroupOwnershipVoter;
use App\Controller\Web\Dashboard\Watering\EditWatering\Input\EditWateringDTO;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

final class Manager
{
    public function __construct(
        private readonly WateringService $wateringService,
        private readonly FormFactoryInterface $formFactory,
        private readonly TranslatorInterface $translator,
        private readonly AuthorizationCheckerInterface $authChecker
    ) {
    }

    public function editFormData(Request $request, Watering $watering): array
    {
        if (!$this->authChecker->isGranted(GroupOwnershipVoter::EDIT, $watering)) {
            $message = $this->translator->trans('security.access_denied.edit');
            throw new AccessDeniedException($message);
        }

        $groupId = $watering->getGroup()->getId();

        $formData = new EditWateringDTO(
            $groupId,
            $watering->getMarker()->getId(),
            $watering->getDetails()->getAmount(),
            $watering->getDetails()->getType()->value,
            $watering->getDetails()->getMethod()->value,
            $watering->getDetails()->getTemperature(),
            $watering->getDescription(),
            $watering->getComment()
        );

        $form = $this->formFactory->create(WateringType::class, $formData, ['group_id' => $groupId]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var EditWateringDTO $editWateringDTO */
            $editWateringDTO = $form->getData();

            if (!$editWateringDTO->groupId) {
                $editWateringDTO->groupId = $groupId;
            }

            $this->wateringService->updateFromEditWateringDTO($watering, $editWateringDTO);

            $message = $this->translator->trans('watering.flash.updated', [], 'messages');
            $request->getSession()->getFlashBag()->add('success', $message);
            return ['success' => true];
        }

        return [
            'form' => $form,
            'watering' => $watering
        ];
    }
}
