<?php

namespace App\Controller\Web\Dashboard\Marker\EditMarker;

use App\Domain\Entity\Marker;
use App\Controller\Form\MarkerType;
use App\Domain\Service\MarkerService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use App\Application\Security\Voter\GroupOwnershipVoter;
use App\Controller\Web\Dashboard\Marker\EditMarker\Input\EditMarkerDTO;
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class Manager
{
    public function __construct(
        private readonly MarkerService $markerService,
        private readonly FormFactoryInterface $formFactory,
        private readonly TranslatorInterface $translator,
        private readonly AuthorizationCheckerInterface $authChecker
    ) {
    }

    public function editFormData(Request $request, Marker $marker): array
    {
        if (!$this->authChecker->isGranted(GroupOwnershipVoter::EDIT, $marker)) {
            $message = $this->translator->trans('security.access_denied.edit');
            throw new AccessDeniedException($message);
        }

        $groupId = $marker->getGroup()->getId();

        $formData = new EditMarkerDTO(
            $groupId,
            $marker->getLetter(),
            $marker->getColor(),
            $marker->getType()->value,
            $marker->getDescription(),
            $marker->getColorDescription(),
        );

        $form = $this->formFactory->create(MarkerType::class, $formData);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var EditMarkerDTO $editMarkerDTO */
            $editMarkerDTO = $form->getData();

            if (!$editMarkerDTO->groupId) {
                $editMarkerDTO->groupId = $groupId;
            }

            $this->markerService->updateFromEditMarkerDTO($marker, $editMarkerDTO);

            $message = $this->translator->trans('marker.flash.updated', [], 'messages');
            $request->getSession()->getFlashBag()->add('success', $message);
            return ['success' => true];
        }

        return [
            'form' => $form,
            'marker' => $marker
        ];
    }
}
