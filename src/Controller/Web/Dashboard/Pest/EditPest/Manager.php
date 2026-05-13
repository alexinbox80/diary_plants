<?php

namespace App\Controller\Web\Dashboard\Pest\EditPest;

use App\Domain\Entity\Pest;
use App\Controller\Form\PestType;
use App\Domain\Service\PestService;
use Symfony\Component\HttpFoundation\Request;
use App\Domain\Exception\AccessDeniedException;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use App\Application\Security\Voter\GroupOwnershipVoter;
use App\Controller\Web\Dashboard\Pest\EditPest\Input\EditPestDTO;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class Manager
{
    public function __construct(
        private readonly PestService $pestService,
        private readonly FormFactoryInterface $formFactory,
        private readonly TranslatorInterface $translator,
        private readonly AuthorizationCheckerInterface $authChecker
    ) {
    }

    public function editFormData(Request $request, Pest $pest): array
    {

        if (!$this->authChecker->isGranted(GroupOwnershipVoter::EDIT, $pest)) {
            $message = $this->translator->trans('security.access_denied.edit');
            throw new AccessDeniedException($message);
        }

        $groupId = $pest->getGroup()->getId();

        $formData = new EditPestDTO(
            $groupId,
            $pest->getMarker()->getId(),
            $pest->getTitle(),
            $pest->getVolume()->getAmount(),
            $pest->getDetails()->getManufacturer(),
            $pest->getVolume()->getApplicationRate(),
            $pest->getDetails()->getDescription(),
            $pest->getDetails()->getComment()
        );

        $form = $this->formFactory->create(PestType::class, $formData, ['group_id' => $groupId]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var EditPestDTO $editPestDTO */
            $editPestDTO = $form->getData();

            if (!$editPestDTO->groupId) {
                $editPestDTO->groupId = $groupId;
            }

            $this->pestService->updateFromEditPestDTO($pest, $editPestDTO);

            $message = $this->translator->trans('pest.flash.updated', [], 'messages');
            $request->getSession()->getFlashBag()->add('success', $message);
            return ['success' => true];
        }

        return [
            'form' => $form,
            'pest' => $pest
        ];
    }
}
