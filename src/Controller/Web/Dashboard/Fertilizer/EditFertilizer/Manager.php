<?php

namespace App\Controller\Web\Dashboard\Fertilizer\EditFertilizer;

use App\Domain\Entity\Fertilizer;
use App\Controller\Form\FertilizerType;
use App\Domain\Service\FertilizerService;
use Symfony\Component\HttpFoundation\Request;
use App\Domain\Exception\AccessDeniedException;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use App\Application\Security\Voter\GroupOwnershipVoter;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use App\Controller\Web\Dashboard\Fertilizer\EditFertilizer\Input\EditFertilizerDTO;

final class Manager
{
    public function __construct(
        private readonly FertilizerService $fertilizerService,
        private readonly FormFactoryInterface $formFactory,
        private readonly TranslatorInterface $translator,
        private readonly AuthorizationCheckerInterface $authChecker
    ) {
    }

    public function editFormData(Request $request, Fertilizer $fertilizer): array
    {
        if (!$this->authChecker->isGranted(GroupOwnershipVoter::EDIT, $fertilizer)) {
            $message = $this->translator->trans('security.access_denied.edit');
            throw new AccessDeniedException($message);
        }

        $groupId = $fertilizer->getGroup()->getId();

        $formData = new EditFertilizerDTO(
            groupId: $groupId,
            markerId: $fertilizer->getMarker()->getId(),
            title: $fertilizer->getTitle(),
            amount: $fertilizer->getVolume()->getAmount(),
            applicationRate: $fertilizer->getVolume()->getApplicationRate(),
            manufacturer: $fertilizer->getDetails()->getManufacturer(),
            description: $fertilizer->getDetails()->getDescription(),
            comment: $fertilizer->getDetails()->getComment()
        );

        $form = $this->formFactory->create(FertilizerType::class, $formData, ['group_id' => $groupId]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var EditFertilizerDTO $editFertilizerDTO */
            $editFertilizerDTO = $form->getData();

            if (!$editFertilizerDTO->groupId) {
                $editFertilizerDTO->groupId = $groupId;
            }

            $this->fertilizerService->updateFromEditFertilizerDTO($fertilizer, $editFertilizerDTO);

            $message = $this->translator->trans('fertilizer.flash.created', [], 'messages');
            $request->getSession()->getFlashBag()->add('success', $message);
            return ['success' => true];
        }

        return [
            'form' => $form,
            'fertilizer' => $fertilizer
        ];
    }
}
