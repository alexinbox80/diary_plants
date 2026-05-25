<?php

namespace App\Controller\Web\Dashboard\Offspring\EditOffspring;

use App\Domain\Entity\Offspring;
use App\Controller\Form\OffspringType;
use App\Domain\Service\OffspringService;
use Symfony\Component\HttpFoundation\Request;
use App\Domain\Exception\AccessDeniedException;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use App\Application\Security\Voter\GroupOwnershipVoter;
use App\Controller\Web\Dashboard\Offspring\EditOffspring\Input\EditOffspringDTO;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class Manager
{
    public function __construct(
        private readonly OffspringService $offspringService,
        private readonly FormFactoryInterface $formFactory,
        private readonly TranslatorInterface $translator,
        private readonly AuthorizationCheckerInterface $authChecker
    ) {
    }

    public function editFormData(Request $request, Offspring $offspring): array
    {
        if (!$this->authChecker->isGranted(GroupOwnershipVoter::EDIT, $offspring)) {
            $message = $this->translator->trans('security.access_denied.edit');
            throw new AccessDeniedException($message);
        }

        $groupId = $offspring->getGroup()->getId();

        $formData = new EditOffspringDTO(
            groupId: $groupId,
            plantId: $offspring->getPlant()->getId(),
            fruitingDate: $offspring->getPhenology()->getFruitingDate(),
            floweringDate: $offspring->getPhenology()->getFloweringDate(),
            mass: $offspring->getFruitMetrics()->getMass(),
            wordColor: $offspring->getFruitMetrics()->getColor(),
            flavor: $offspring->getFruitMetrics()->getFlavor(),
            quantity: $offspring->getFruitMetrics()->getQuantity(),
            comment: $offspring->getComment()
        );

        $form = $this->formFactory->create(OffspringType::class, $formData, ['group_id' => $groupId]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var EditOffspringDTO $editOffspringDTO */
            $editOffspringDTO = $form->getData();

            if (!$editOffspringDTO->groupId) {
                $editOffspringDTO->groupId = $groupId;
            }

            $this->offspringService->updateFromEditOffspringDTO($offspring, $editOffspringDTO);

            $message = $this->translator->trans('offspring.flash.updated', [], 'messages');
            $request->getSession()->getFlashBag()->add('success', $message);
            return ['success' => true];
        }

        return [
            'form' => $form,
            'offspring' => $offspring
        ];
    }
}
